<?php

namespace Staatic\Vendor\GuzzleHttp\Cookie;

use Countable;
use IteratorAggregate;
use Staatic\Vendor\Psr\Http\Message\RequestInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
interface CookieJarInterface extends Countable, IteratorAggregate
{
    /**
     * @param RequestInterface $request
     */
    public function withCookieHeader($request) : RequestInterface;
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return void
     */
    public function extractCookies($request, $response);
    /**
     * @param SetCookie $cookie
     */
    public function setCookie($cookie) : bool;
    /**
     * @param string|null $domain
     * @param string|null $path
     * @param string|null $name
     * @return void
     */
    public function clear($domain = null, $path = null, $name = null);
    /**
     * @return void
     */
    public function clearSessionCookies();
    public function toArray() : array;
}
