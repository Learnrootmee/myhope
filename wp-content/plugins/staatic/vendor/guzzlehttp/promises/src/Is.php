<?php

namespace Staatic\Vendor\GuzzleHttp\Promise;

final class Is
{
    public static function pending(PromiseInterface $promise)
    {
        return $promise->getState() === PromiseInterface::PENDING;
    }
    public static function settled(PromiseInterface $promise)
    {
        return $promise->getState() !== PromiseInterface::PENDING;
    }
    public static function fulfilled(PromiseInterface $promise)
    {
        return $promise->getState() === PromiseInterface::FULFILLED;
    }
    public static function rejected(PromiseInterface $promise)
    {
        return $promise->getState() === PromiseInterface::REJECTED;
    }
}
