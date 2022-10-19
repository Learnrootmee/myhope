<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

class MergeBuilder
{
    protected $node;
    public $allowFalse = \false;
    public $allowOverwrite = \true;
    public function __construct(NodeDefinition $node)
    {
        $this->node = $node;
    }
    /**
     * @return $this
     * @param bool $allow
     */
    public function allowUnset($allow = \true)
    {
        $this->allowFalse = $allow;
        return $this;
    }
    /**
     * @return $this
     * @param bool $deny
     */
    public function denyOverwrite($deny = \true)
    {
        $this->allowOverwrite = !$deny;
        return $this;
    }
    /**
     * @return NodeDefinition|ArrayNodeDefinition|\Staatic\Vendor\Symfony\Component\Config\Definition\Builder\VariableNodeDefinition
     */
    public function end()
    {
        return $this->node;
    }
}
