<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Argument;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
class ServiceLocatorArgument implements ArgumentInterface
{
    use ReferenceSetArgumentTrait;
    private $taggedIteratorArgument = null;
    /**
     * @param mixed[]|TaggedIteratorArgument $values
     */
    public function __construct($values = [])
    {
        if ($values instanceof TaggedIteratorArgument) {
            $this->taggedIteratorArgument = $values;
            $this->values = [];
        } else {
            $this->setValues($values);
        }
    }
    /**
     * @return TaggedIteratorArgument|null
     */
    public function getTaggedIteratorArgument()
    {
        return $this->taggedIteratorArgument;
    }
}
