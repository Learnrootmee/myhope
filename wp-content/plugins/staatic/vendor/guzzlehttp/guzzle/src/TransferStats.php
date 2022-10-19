<?php

namespace Staatic\Vendor\GuzzleHttp;

use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class TransferStats
{
    private $request;
    private $response;
    private $transferTime;
    private $handlerStats;
    private $handlerErrorData;
    /**
     * @param ResponseInterface|null $response
     * @param float|null $transferTime
     */
    public function __construct(RequestInterface $request, $response = null, $transferTime = null, $handlerErrorData = null, array $handlerStats = [])
    {
        $this->request = $request;
        $this->response = $response;
        $this->transferTime = $transferTime;
        $this->handlerErrorData = $handlerErrorData;
        $this->handlerStats = $handlerStats;
    }
    public function getRequest() : RequestInterface
    {
        return $this->request;
    }
    /**
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }
    public function hasResponse() : bool
    {
        return $this->response !== null;
    }
    public function getHandlerErrorData()
    {
        return $this->handlerErrorData;
    }
    public function getEffectiveUri() : UriInterface
    {
        return $this->request->getUri();
    }
    /**
     * @return float|null
     */
    public function getTransferTime()
    {
        return $this->transferTime;
    }
    public function getHandlerStats() : array
    {
        return $this->handlerStats;
    }
    public function getHandlerStat(string $stat)
    {
        return $this->handlerStats[$stat] ?? null;
    }
}
