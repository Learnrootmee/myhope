<?php

namespace Staatic\Framework\ConfigGenerator;

use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Framework\Result;
use Staatic\Framework\Util\PathHelper;
final class NginxConfigGenerator extends AbstractConfigGenerator
{
    /**
     * @var mixed[]
     */
    private $errorDocuments = [];
    /**
     * @var mixed[]
     */
    private $redirects = [];
    /**
     * @var mixed[]
     */
    private $contentTypeOverrides = [];
    /**
     * @param string|null $notFoundPath
     */
    public function __construct($notFoundPath = null)
    {
        if ($notFoundPath) {
            $this->errorDocuments[404] = $notFoundPath;
        }
    }
    /**
     * @param Result $result
     * @return void
     */
    public function processResult($result)
    {
        if ($result->redirectUrl()) {
            $this->redirects[$result->url()->getPath()] = ['redirectUrl' => $result->redirectUrl(), 'statusCode' => $result->statusCode()];
        } elseif ($this->hasNonStandardMimeType($result) || $this->hasNonUtf8Charset($result)) {
            $this->contentTypeOverrides[$result->url()->getPath()] = ['mimeType' => $result->mimeType(), 'charset' => $result->charset()];
        }
    }
    public function getFiles() : array
    {
        return ['/nginx_rules.conf' => $this->generateFile()];
    }
    private function generateFile() : StreamInterface
    {
        $stream = Utils::streamFor();
        foreach ($this->errorDocuments as $statusCode => $path) {
            $stream->write(\sprintf("error_page %d %s;\n", $statusCode, PathHelper::determineFilePath($path)));
        }
        $rulesPerPath = [];
        foreach ($this->errorDocuments as $statusCode => $path) {
            if (!isset($rulesPerPath[$path])) {
                $rulesPerPath[$path] = [];
            }
            $rulesPerPath[$path][] = 'internal;';
        }
        foreach ($this->redirects as $path => $detail) {
            if (!isset($rulesPerPath[$path])) {
                $rulesPerPath[$path] = [];
            }
            $rulesPerPath[$path][] = \sprintf('return %d %s;', $detail['statusCode'], $detail['redirectUrl']);
        }
        foreach ($this->contentTypeOverrides as $path => $detail) {
            if (!isset($rulesPerPath[$path])) {
                $rulesPerPath[$path] = [];
            }
            $rulesPerPath[$path][] = \sprintf('types { } default_type "%s";', $detail['charset'] ? \sprintf('%s; charset=%s', $detail['mimeType'], $detail['charset']) : $detail['mimeType']);
        }
        foreach ($rulesPerPath as $path => $rules) {
            $stream->write(\sprintf("location ~ ^%s\$ { %s }\n", $path, \implode(" ", $rules)));
        }
        $stream->rewind();
        return $stream;
    }
}
