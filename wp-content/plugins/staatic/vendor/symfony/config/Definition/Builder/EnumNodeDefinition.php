<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use InvalidArgumentException;
use RuntimeException;
use Staatic\Vendor\Symfony\Component\Config\Definition\EnumNode;
class EnumNodeDefinition extends ScalarNodeDefinition
{
    /**
     * @var mixed[]
     */
    private $values;
    /**
     * @return $this
     * @param mixed[] $values
     */
    public function values($values)
    {
        $values = \array_unique($values);
        if (empty($values)) {
            throw new InvalidArgumentException('->values() must be called with at least one value.');
        }
        $this->values = $values;
        return $this;
    }
    protected function instantiateNode() : EnumNode
    {
        if (!isset($this->values)) {
            throw new RuntimeException('You must call ->values() on enum nodes.');
        }
        return new EnumNode($this->name, $this->parent, $this->values, $this->pathSeparator);
    }
}
