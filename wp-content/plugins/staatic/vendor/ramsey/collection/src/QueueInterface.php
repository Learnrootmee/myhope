<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use Staatic\Vendor\Ramsey\Collection\Exception\NoSuchElementException;
interface QueueInterface extends ArrayInterface
{
    public function add($element) : bool;
    public function element();
    public function offer($element) : bool;
    public function peek();
    public function poll();
    public function remove();
    public function getType() : string;
}
