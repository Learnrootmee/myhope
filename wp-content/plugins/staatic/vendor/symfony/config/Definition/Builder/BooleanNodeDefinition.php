<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use Staatic\Vendor\Symfony\Component\Config\Definition\BooleanNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
class BooleanNodeDefinition extends ScalarNodeDefinition
{
    /**
     * @param string|null $name
     */
    public function __construct($name, NodeParentInterface $parent = null)
    {
        parent::__construct($name, $parent);
        $this->nullEquivalent = \true;
    }
    protected function instantiateNode() : BooleanNode
    {
        return new BooleanNode($this->name, $this->parent, $this->pathSeparator);
    }
    /**
     * @return $this
     */
    public function cannotBeEmpty()
    {
        throw new InvalidDefinitionException('->cannotBeEmpty() is not applicable to BooleanNodeDefinition.');
    }
}
