<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Response;

use Staatic\Vendor\GuzzleHttp\Promise\Create;
use Staatic\Vendor\GuzzleHttp\Promise\PromiseInterface as GuzzlePromiseInterface;
use Staatic\Vendor\Http\Promise\Promise as HttplugPromiseInterface;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
final class HttplugPromise implements HttplugPromiseInterface
{
    private $promise;
    public function __construct(GuzzlePromiseInterface $promise)
    {
        $this->promise = $promise;
    }
    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     */
    public function then($onFulfilled = null, $onRejected = null) : self
    {
        return new self($this->promise->then($this->wrapThenCallback($onFulfilled), $this->wrapThenCallback($onRejected)));
    }
    /**
     * @return void
     */
    public function cancel()
    {
        $this->promise->cancel();
    }
    public function getState() : string
    {
        return $this->promise->getState();
    }
    /**
     * @return mixed
     */
    public function wait($unwrap = \true)
    {
        $result = $this->promise->wait($unwrap);
        while ($result instanceof HttplugPromiseInterface || $result instanceof GuzzlePromiseInterface) {
            $result = $result->wait($unwrap);
        }
        return $result;
    }
    /**
     * @param callable|null $callback
     * @return callable|null
     */
    private function wrapThenCallback($callback)
    {
        if (null === $callback) {
            return null;
        }
        return static function ($value) use($callback) {
            return Create::promiseFor($callback($value));
        };
    }
}
