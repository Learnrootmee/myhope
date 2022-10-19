<?php

namespace Staatic\Vendor\GuzzleHttp\Promise;

use InvalidArgumentException;
use Throwable;
use Exception;
use LogicException;
class RejectedPromise implements PromiseInterface
{
    private $reason;
    public function __construct($reason)
    {
        if (\is_object($reason) && \method_exists($reason, 'then')) {
            throw new InvalidArgumentException('You cannot create a RejectedPromise with a promise.');
        }
        $this->reason = $reason;
    }
    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     */
    public function then($onFulfilled = null, $onRejected = null)
    {
        if (!$onRejected) {
            return $this;
        }
        $queue = Utils::queue();
        $reason = $this->reason;
        $p = new Promise([$queue, 'run']);
        $queue->add(static function () use($p, $reason, $onRejected) {
            if (Is::pending($p)) {
                try {
                    $p->resolve($onRejected($reason));
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
        if ($unwrap) {
            throw Create::exceptionFor($this->reason);
        }
        return null;
    }
    public function getState()
    {
        return self::REJECTED;
    }
    public function resolve($value)
    {
        throw new LogicException("Cannot resolve a rejected promise");
    }
    public function reject($reason)
    {
        if ($reason !== $this->reason) {
            throw new LogicException("Cannot reject a rejected promise");
        }
    }
    public function cancel()
    {
    }
}
