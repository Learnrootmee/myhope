<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient;

use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\ResponseStreamInterface;
use Staatic\Vendor\Symfony\Contracts\Service\ResetInterface;
trait DecoratorTrait
{
    private $client;
    public function __construct(HttpClientInterface $client = null)
    {
        $this->client = $client ?? HttpClient::create();
    }
    /**
     * @param string $method
     * @param string $url
     * @param mixed[] $options
     */
    public function request($method, $url, $options = []) : ResponseInterface
    {
        return $this->client->request($method, $url, $options);
    }
    /**
     * @param ResponseInterface|mixed[] $responses
     * @param float|null $timeout
     */
    public function stream($responses, $timeout = null) : ResponseStreamInterface
    {
        return $this->client->stream($responses, $timeout);
    }
    /**
     * @return $this
     * @param mixed[] $options
     */
    public function withOptions($options)
    {
        $clone = clone $this;
        $clone->client = $this->client->withOptions($options);
        return $clone;
    }
    public function reset()
    {
        if ($this->client instanceof ResetInterface) {
            $this->client->reset();
        }
    }
}
