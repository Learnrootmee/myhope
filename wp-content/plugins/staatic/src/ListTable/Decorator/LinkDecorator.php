<?php

declare(strict_types=1);

namespace Staatic\WordPress\ListTable\Decorator;

final class LinkDecorator implements DecoratorInterface
{
    /**
     * @var \Closure
     */
    private $hrefLocator;

    /**
     * @var bool
     */
    private $targetBlank;

    public function __construct(callable $hrefLocator, bool $targetBlank = \false)
    {
        $callable = $hrefLocator;
        $this->hrefLocator = function () use ($callable) {
            return $callable(...func_get_args());
        };
        $this->targetBlank = $targetBlank;
    }

    /**
     * @param string $input
     */
    public function decorate($input, $item) : string
    {
        $href = ($this->hrefLocator)($item);
        if ($href === null) {
            return $input;
        }

        return \sprintf(
            '<a href="%s"%s>%s</a>',
            \esc_url($href),
            $this->targetBlank ? ' target="_blank" rel="noopener"' : '',
            $input
        );
    }
}
