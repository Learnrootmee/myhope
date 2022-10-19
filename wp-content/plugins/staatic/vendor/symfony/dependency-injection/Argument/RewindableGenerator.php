<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Argument;

use IteratorAggregate;
use Countable;
use Closure;
use Traversable;
class RewindableGenerator implements IteratorAggregate, Countable
{
    /**
     * @var Closure
     */
    private $generator;
    /**
     * @var Closure|int
     */
    private $count;
    /**
     * @param int|callable $count
     */
    public function __construct(callable $generator, $count)
    {
        $callable = $generator;
        $this->generator = $generator instanceof Closure ? $generator : function () use ($callable) {
            return $callable(...func_get_args());
        };
        $callable = $count;
        $this->count = \is_callable($count) && !$count instanceof Closure ? function () use ($callable) {
            return $callable(...func_get_args());
        } : $count;
    }
    public function getIterator() : Traversable
    {
        $g = $this->generator;
        return $g();
    }
    public function count() : int
    {
        if (\is_callable($count = $this->count)) {
            $this->count = $count();
        }
        return $this->count;
    }
}
