<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\Decorator;

final class TitleDecorator implements DecoratorInterface
{
    /**
     * @var \Closure
     */
    private $titleCallback;

    public function __construct(callable $titleCallback)
    {
        $callable = $titleCallback;
        $this->titleCallback = function () use ($callable) {
            return $callable(...func_get_args());
        };
    }

    /**
     * @param string $input
     */
    public function decorate($input, $item) : string
    {
        $title = ($this->titleCallback)($item);
        if ($title === null) {
            return $input;
        }
        if (strncmp($input, '<a ', strlen('<a ')) === 0) {
            return \str_replace('<a ', \sprintf('<a title="%s" ', \esc_attr($title)), $input);
        }

        return \sprintf('<span title="%s">%s</a>', \esc_attr($title), $input);
    }
}
