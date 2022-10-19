<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

abstract class AbstractSet extends AbstractCollection
{
    public function add($element) : bool
    {
        if ($this->contains($element)) {
            return \false;
        }
        return parent::add($element);
    }
    /**
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($this->contains($value)) {
            return;
        }
        parent::offsetSet($offset, $value);
    }
}
