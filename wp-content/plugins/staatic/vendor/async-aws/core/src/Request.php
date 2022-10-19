<?php

namespace Staatic\Vendor\AsyncAws\Core;

use Staatic\Vendor\AsyncAws\Core\Exception\LogicException;
use Staatic\Vendor\AsyncAws\Core\Stream\RequestStream;
class Request
{
    private $method;
    private $uri;
    private $headers;
    private $body;
    private $query;
    private $endpoint;
    private $parsed;
    public function __construct(string $method, string $uri, array $query, array $headers, RequestStream $body)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = [];
        foreach ($headers as $key => $value) {
            $this->headers[\strtolower($key)] = (string) $value;
        }
        $this->body = $body;
        $this->query = $query;
        $this->endpoint = '';
    }
    public function getMethod() : string
    {
        return $this->method;
    }
    /**
     * @param string $method
     * @return void
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }
    public function getUri() : string
    {
        return $this->uri;
    }
    public function hasHeader($name) : bool
    {
        return \array_key_exists(\strtolower($name), $this->headers);
    }
    /**
     * @param string|null $value
     * @return void
     */
    public function setHeader($name, $value)
    {
        $this->headers[\strtolower($name)] = $value;
    }
    public function getHeaders() : array
    {
        return $this->headers;
    }
    /**
     * @param string $name
     * @return string|null
     */
    public function getHeader($name)
    {
        return $this->headers[\strtolower($name)] ?? null;
    }
    /**
     * @param string $name
     * @return void
     */
    public function removeHeader($name)
    {
        unset($this->headers[\strtolower($name)]);
    }
    public function getBody() : RequestStream
    {
        return $this->body;
    }
    /**
     * @param RequestStream $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
    public function hasQueryAttribute($name) : bool
    {
        return \array_key_exists($name, $this->query);
    }
    /**
     * @return void
     */
    public function removeQueryAttribute($name)
    {
        unset($this->query[$name]);
        $this->endpoint = '';
    }
    /**
     * @return void
     */
    public function setQueryAttribute($name, $value)
    {
        $this->query[$name] = $value;
        $this->endpoint = '';
    }
    /**
     * @param string $name
     * @return string|null
     */
    public function getQueryAttribute($name)
    {
        return $this->query[$name] ?? null;
    }
    public function getQuery() : array
    {
        return $this->query;
    }
    public function getEndpoint() : string
    {
        if (empty($this->endpoint)) {
            $this->endpoint = $this->parsed['scheme'] . '://' . $this->parsed['host'] . (isset($this->parsed['port']) ? ':' . $this->parsed['port'] : '') . $this->uri . ($this->query ? (\false === \strpos($this->uri, '?') ? '?' : '&') . \http_build_query($this->query) : '');
        }
        return $this->endpoint;
    }
    /**
     * @param string $endpoint
     * @return void
     */
    public function setEndpoint($endpoint)
    {
        if (!empty($this->endpoint)) {
            throw new LogicException('Request::$endpoint cannot be changed after it has a value.');
        }
        $this->endpoint = $endpoint;
        $this->parsed = \parse_url($this->endpoint);
        \parse_str($this->parsed['query'] ?? '', $this->query);
        $this->uri = $this->parsed['path'] ?? '/';
    }
}
