<?php

namespace Staatic\Vendor\GuzzleHttp\Exception;

use Throwable;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
class BadResponseException extends RequestException
{
    public function __construct(string $message, RequestInterface $request, ResponseInterface $response, Throwable $previous = null, array $handlerContext = [])
    {
        parent::__construct($message, $request, $response, $previous, $handlerContext);
    }
    public function hasResponse() : bool
    {
        return \true;
    }
    /**
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return parent::getResponse();
    }
}
