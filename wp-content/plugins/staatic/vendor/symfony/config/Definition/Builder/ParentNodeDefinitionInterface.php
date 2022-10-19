<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

interface ParentNodeDefinitionInterface extends BuilderAwareInterface
{
    public function children() : NodeBuilder;
    /**
     * @return $this
     * @param NodeDefinition $node
     */
    public function append($node);
    public function getChildNodeDefinitions() : array;
}
