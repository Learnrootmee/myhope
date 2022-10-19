<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\Decorator;

final class CallbackDecorator implements DecoratorInterface
{
    /**
     * @var \Closure
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $callable = $callback;
        $this->callback = function () use ($callable) {
            return $callable(...func_get_args());
        };
    }

    /**
     * @param string $input
     */
    public function decorate($input, $item) : string
    {
        return ($this->callback)($input, $item);
    }
}
