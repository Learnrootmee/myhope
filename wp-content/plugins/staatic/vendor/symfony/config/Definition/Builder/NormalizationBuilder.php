<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use Closure;
class NormalizationBuilder
{
    protected $node;
    public $before = [];
    public $remappings = [];
    public function __construct(NodeDefinition $node)
    {
        $this->node = $node;
    }
    /**
     * @return $this
     * @param string $key
     * @param string|null $plural
     */
    public function remap($key, $plural = null)
    {
        $this->remappings[] = [$key, null === $plural ? $key . 's' : $plural];
        return $this;
    }
    /**
     * @return ExprBuilder|$this
     * @param Closure|null $closure
     */
    public function before($closure = null)
    {
        if (null !== $closure) {
            $this->before[] = $closure;
            return $this;
        }
        return $this->before[] = new ExprBuilder($this->node);
    }
}
