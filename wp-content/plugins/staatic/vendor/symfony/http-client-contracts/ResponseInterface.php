<?php

namespace Staatic\Vendor\Symfony\Contracts\HttpClient;

use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
interface ResponseInterface
{
    public function getStatusCode() : int;
    /**
     * @param bool $throw
     */
    public function getHeaders($throw = \true) : array;
    /**
     * @param bool $throw
     */
    public function getContent($throw = \true) : string;
    /**
     * @param bool $throw
     */
    public function toArray($throw = \true) : array;
    /**
     * @return void
     */
    public function cancel();
    /**
     * @return mixed
     * @param string|null $type
     */
    public function getInfo($type = null);
}
