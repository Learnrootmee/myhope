<?php

namespace Staatic\Vendor\GuzzleHttp\Promise;

use InvalidArgumentException;
use Throwable;
use Exception;
use LogicException;
class FulfilledPromise implements PromiseInterface
{
    private $value;
    public function __construct($value)
    {
        if (\is_object($value) && \method_exists($value, 'then')) {
            throw new InvalidArgumentException('You cannot create a FulfilledPromise with a promise.');
        }
        $this->value = $value;
    }
    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     */
    public function then($onFulfilled = null, $onRejected = null)
    {
        if (!$onFulfilled) {
            return $this;
        }
        $queue = Utils::queue();
        $p = new Promise([$queue, 'run']);
        $value = $this->value;
        $queue->add(static function () use($p, $value, $onFulfilled) {
            if (Is::pending($p)) {
                try {
                    $p->resolve($onFulfilled($value));
                } catch (Throwable $e) {
                    $p->reject($e);
                } catch (Exception $e) {
                    $p->reject($e);
                }
            }
        });
        return $p;
    }
    /**
     * @param callable $onRejected
     */
    public function otherwise($onRejected)
    {
        return $this->then(null, $onRejected);
    }
    public function wait($unwrap = \true, $defaultDelivery = null)
    {
        return $unwrap ? $this->value : null;
    }
    public function getState()
    {
        return self::FULFILLED;
    }
    public function resolve($value)
    {
        if ($value !== $this->value) {
            throw new LogicException("Cannot resolve a fulfilled promise");
        }
    }
    public function reject($reason)
    {
        throw new LogicException("Cannot reject a fulfilled promise");
    }
    public function cancel()
    {
    }
}
