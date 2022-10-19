<?php

namespace Staatic\Crawler\Observer;

use Closure;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\GuzzleHttp\Exception\TransferException;
final class CallbackObserver extends AbstractObserver
{
    /**
     * @var \Closure
     */
    private $crawlFulfilled;
    /**
     * @var \Closure
     */
    private $crawlRejected;
    /**
     * @var \Closure|null
     */
    private $startsCrawling;
    /**
     * @var \Closure|null
     */
    private $finishedCrawling;
    /**
     * @param callable|null $startsCrawling
     * @param callable|null $finishedCrawling
     */
    public function __construct(callable $crawlFulfilled, callable $crawlRejected, $startsCrawling = null, $finishedCrawling = null)
    {
        $callable = $crawlFulfilled;
        $this->crawlFulfilled = function () use ($callable) {
            return $callable(...func_get_args());
        };
        $callable = $crawlRejected;
        $this->crawlRejected = function () use ($callable) {
            return $callable(...func_get_args());
        };
        $callable = $startsCrawling;
        $this->startsCrawling = $startsCrawling ? function () use ($callable) {
            return $callable(...func_get_args());
        } : null;
        $callable = $finishedCrawling;
        $this->finishedCrawling = $finishedCrawling ? function () use ($callable) {
            return $callable(...func_get_args());
        } : null;
    }
    /**
     * @return void
     */
    public function startsCrawling()
    {
        if (!$this->startsCrawling) {
            return;
        }
        ($this->startsCrawling)();
    }
    /**
     * @param UriInterface $url
     * @param UriInterface $transformedUrl
     * @param ResponseInterface $response
     * @param UriInterface|null $foundOnUrl
     * @param mixed[] $tags
     * @return void
     */
    public function crawlFulfilled($url, $transformedUrl, $response, $foundOnUrl, $tags)
    {
        ($this->crawlFulfilled)($url, $transformedUrl, $response, $foundOnUrl, $tags);
    }
    /**
     * @param UriInterface $url
     * @param UriInterface $transformedUrl
     * @param TransferException $transferException
     * @param UriInterface|null $foundOnUrl
     * @param mixed[] $tags
     * @return void
     */
    public function crawlRejected($url, $transformedUrl, $transferException, $foundOnUrl, $tags)
    {
        ($this->crawlRejected)($url, $transformedUrl, $transferException, $foundOnUrl, $tags);
    }
    /**
     * @return void
     */
    public function finishedCrawling()
    {
        if (!$this->finishedCrawling) {
            return;
        }
        ($this->finishedCrawling)();
    }
}
