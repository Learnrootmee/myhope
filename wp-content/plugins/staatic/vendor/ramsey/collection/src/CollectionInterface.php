<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

interface CollectionInterface extends ArrayInterface
{
    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';
    public function add($element) : bool;
    /**
     * @param bool $strict
     */
    public function contains($element, $strict = \true) : bool;
    public function getType() : string;
    public function remove($element) : bool;
    /**
     * @param string $propertyOrMethod
     */
    public function column($propertyOrMethod) : array;
    public function first();
    public function last();
    /**
     * @param string $propertyOrMethod
     * @param string $order
     */
    public function sort($propertyOrMethod, $order = self::SORT_ASC) : self;
    /**
     * @param callable $callback
     */
    public function filter($callback) : self;
    /**
     * @param string $propertyOrMethod
     */
    public function where($propertyOrMethod, $value) : self;
    /**
     * @param callable $callback
     */
    public function map($callback) : self;
    /**
     * @param \Staatic\Vendor\Ramsey\Collection\CollectionInterface $other
     */
    public function diff($other) : self;
    /**
     * @param \Staatic\Vendor\Ramsey\Collection\CollectionInterface $other
     */
    public function intersect($other) : self;
    /**
     * @param \Staatic\Vendor\Ramsey\Collection\CollectionInterface ...$collections
     */
    public function merge(...$collections) : self;
}
