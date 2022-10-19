<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Serializable;
interface ArrayInterface extends ArrayAccess, Countable, IteratorAggregate, Serializable
{
    /**
     * @return void
     */
    public function clear();
    public function toArray() : array;
    public function isEmpty() : bool;
}
