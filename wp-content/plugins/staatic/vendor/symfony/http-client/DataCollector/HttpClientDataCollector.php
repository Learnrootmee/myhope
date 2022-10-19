<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\DataCollector;

use Throwable;
use Staatic\Vendor\Symfony\Component\HttpClient\TraceableHttpClient;
use Staatic\Vendor\Symfony\Component\HttpFoundation\Request;
use Staatic\Vendor\Symfony\Component\HttpFoundation\Response;
use Staatic\Vendor\Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Staatic\Vendor\Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use Staatic\Vendor\Symfony\Component\VarDumper\Caster\ImgStub;
final class HttpClientDataCollector extends DataCollector implements LateDataCollectorInterface
{
    /**
     * @var mixed[]
     */
    private $clients = [];
    /**
     * @param string $name
     * @param TraceableHttpClient $client
     */
    public function registerClient($name, $client)
    {
        $this->clients[$name] = $client;
    }
    /**
     * @param \Staatic\Vendor\Symfony\Component\HttpFoundation\Request $request
     * @param \Staatic\Vendor\Symfony\Component\HttpFoundation\Response $response
     * @param Throwable|null $exception
     */
    public function collect($request, $response, $exception = null)
    {
        $this->reset();
        foreach ($this->clients as $name => $client) {
            list($errorCount, $traces) = $this->collectOnClient($client);
            $this->data['clients'][$name] = ['traces' => $traces, 'error_count' => $errorCount];
            $this->data['request_count'] += \count($traces);
            $this->data['error_count'] += $errorCount;
        }
    }
    public function lateCollect()
    {
        foreach ($this->clients as $client) {
            $client->reset();
        }
    }
    public function getClients() : array
    {
        return $this->data['clients'] ?? [];
    }
    public function getRequestCount() : int
    {
        return $this->data['request_count'] ?? 0;
    }
    public function getErrorCount() : int
    {
        return $this->data['error_count'] ?? 0;
    }
    public function getName() : string
    {
        return 'http_client';
    }
    public function reset()
    {
        $this->data = ['clients' => [], 'request_count' => 0, 'error_count' => 0];
    }
    private function collectOnClient(TraceableHttpClient $client) : array
    {
        $traces = $client->getTracedRequests();
        $errorCount = 0;
        $baseInfo = ['response_headers' => 1, 'retry_count' => 1, 'redirect_count' => 1, 'redirect_url' => 1, 'user_data' => 1, 'error' => 1, 'url' => 1];
        foreach ($traces as $i => $trace) {
            if (400 <= ($trace['info']['http_code'] ?? 0)) {
                ++$errorCount;
            }
            $info = $trace['info'];
            $traces[$i]['http_code'] = $info['http_code'] ?? 0;
            unset($info['filetime'], $info['http_code'], $info['ssl_verify_result'], $info['content_type']);
            if (($info['http_method'] ?? null) === $trace['method']) {
                unset($info['http_method']);
            }
            if (($info['url'] ?? null) === $trace['url']) {
                unset($info['url']);
            }
            foreach ($info as $k => $v) {
                if (!$v || \is_numeric($v) && 0 > $v) {
                    unset($info[$k]);
                }
            }
            if (\is_string($content = $trace['content'])) {
                $contentType = 'application/octet-stream';
                foreach ($info['response_headers'] ?? [] as $h) {
                    if (0 === \stripos($h, 'content-type: ')) {
                        $contentType = \substr($h, \strlen('content-type: '));
                        break;
                    }
                }
                if (0 === \strpos($contentType, 'image/') && \class_exists(ImgStub::class)) {
                    $content = new ImgStub($content, $contentType, '');
                } else {
                    $content = [$content];
                }
                $content = ['response_content' => $content];
            } elseif (\is_array($content)) {
                $content = ['response_json' => $content];
            } else {
                $content = [];
            }
            if (isset($info['retry_count'])) {
                $content['retries'] = $info['previous_info'];
                unset($info['previous_info']);
            }
            $debugInfo = \array_diff_key($info, $baseInfo);
            $info = ['info' => $debugInfo] + \array_diff_key($info, $debugInfo) + $content;
            unset($traces[$i]['info']);
            $traces[$i]['info'] = $this->cloneVar($info);
            $traces[$i]['options'] = $this->cloneVar($trace['options']);
        }
        return [$errorCount, $traces];
    }
}
