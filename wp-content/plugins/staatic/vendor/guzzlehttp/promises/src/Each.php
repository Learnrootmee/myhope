<?php

namespace Staatic\Vendor\GuzzleHttp\Promise;

final class Each
{
    public static function of($iterable, callable $onFulfilled = null, callable $onRejected = null)
    {
        return (new EachPromise($iterable, ['fulfilled' => $onFulfilled, 'rejected' => $onRejected]))->promise();
    }
    public static function ofLimit($iterable, $concurrency, callable $onFulfilled = null, callable $onRejected = null)
    {
        return (new EachPromise($iterable, ['fulfilled' => $onFulfilled, 'rejected' => $onRejected, 'concurrency' => $concurrency]))->promise();
    }
    public static function ofLimitAll($iterable, $concurrency, callable $onFulfilled = null)
    {
        return each_limit($iterable, $concurrency, $onFulfilled, function ($reason, $idx, PromiseInterface $aggregate) {
            $aggregate->reject($reason);
        });
    }
}
