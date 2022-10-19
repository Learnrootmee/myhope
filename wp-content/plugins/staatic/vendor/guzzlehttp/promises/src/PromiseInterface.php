<?php

namespace Staatic\Vendor\GuzzleHttp\Promise;

interface PromiseInterface
{
    const PENDING = 'pending';
    const FULFILLED = 'fulfilled';
    const REJECTED = 'rejected';
    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     */
    public function then($onFulfilled = null, $onRejected = null);
    /**
     * @param callable $onRejected
     */
    public function otherwise($onRejected);
    public function getState();
    public function resolve($value);
    public function reject($reason);
    public function cancel();
    public function wait($unwrap = \true);
}
