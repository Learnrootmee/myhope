<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use Closure;
use Staatic\Vendor\Ramsey\Collection\Exception\CollectionMismatchException;
use Staatic\Vendor\Ramsey\Collection\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Collection\Exception\InvalidSortOrderException;
use Staatic\Vendor\Ramsey\Collection\Exception\OutOfBoundsException;
use Staatic\Vendor\Ramsey\Collection\Tool\TypeTrait;
use Staatic\Vendor\Ramsey\Collection\Tool\ValueExtractorTrait;
use Staatic\Vendor\Ramsey\Collection\Tool\ValueToStringTrait;
use function array_filter;
use function array_map;
use function array_merge;
use function array_search;
use function array_udiff;
use function array_uintersect;
use function current;
use function end;
use function in_array;
use function is_int;
use function reset;
use function sprintf;
use function unserialize;
use function usort;
abstract class AbstractCollection extends AbstractArray implements CollectionInterface
{
    use TypeTrait;
    use ValueToStringTrait;
    use ValueExtractorTrait;
    public function add($element) : bool
    {
        $this[] = $element;
        return \true;
    }
    /**
     * @param bool $strict
     */
    public function contains($element, $strict = \true) : bool
    {
        return in_array($element, $this->data, $strict);
    }
    /**
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($this->checkType($this->getType(), $value) === \false) {
            throw new InvalidArgumentException('Value must be of type ' . $this->getType() . '; value is ' . $this->toolValueToString($value));
        }
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }
    public function remove($element) : bool
    {
        if (($position = array_search($element, $this->data, \true)) !== \false) {
            unset($this->data[$position]);
            return \true;
        }
        return \false;
    }
    /**
     * @param string $propertyOrMethod
     */
    public function column($propertyOrMethod) : array
    {
        $temp = [];
        foreach ($this->data as $item) {
            $value = $this->extractValue($item, $propertyOrMethod);
            $temp[] = $value;
        }
        return $temp;
    }
    public function first()
    {
        if ($this->isEmpty()) {
            throw new OutOfBoundsException('Can\'t determine first item. Collection is empty');
        }
        reset($this->data);
        $first = current($this->data);
        return $first;
    }
    public function last()
    {
        if ($this->isEmpty()) {
            throw new OutOfBoundsException('Can\'t determine last item. Collection is empty');
        }
        $item = end($this->data);
        reset($this->data);
        return $item;
    }
    /**
     * @param string $propertyOrMethod
     * @param string $order
     */
    public function sort($propertyOrMethod, $order = self::SORT_ASC) : CollectionInterface
    {
        if (!in_array($order, [self::SORT_ASC, self::SORT_DESC], \true)) {
            throw new InvalidSortOrderException('Invalid sort order given: ' . $order);
        }
        $collection = clone $this;
        usort($collection->data, function ($a, $b) use($propertyOrMethod, $order) : int {
            $aValue = $this->extractValue($a, $propertyOrMethod);
            $bValue = $this->extractValue($b, $propertyOrMethod);
            return ($aValue <=> $bValue) * ($order === self::SORT_DESC ? -1 : 1);
        });
        return $collection;
    }
    /**
     * @param callable $callback
     */
    public function filter($callback) : CollectionInterface
    {
        $collection = clone $this;
        $collection->data = array_merge([], array_filter($collection->data, $callback === null ? function ($v, $k) : bool {
            return !empty($v);
        } : $callback, $callback === null ? ARRAY_FILTER_USE_BOTH : 0));
        return $collection;
    }
    /**
     * @param string $propertyOrMethod
     */
    public function where($propertyOrMethod, $value) : CollectionInterface
    {
        return $this->filter(function ($item) use($propertyOrMethod, $value) {
            $accessorValue = $this->extractValue($item, $propertyOrMethod);
            return $accessorValue === $value;
        });
    }
    /**
     * @param callable $callback
     */
    public function map($callback) : CollectionInterface
    {
        return new Collection('mixed', array_map($callback, $this->data));
    }
    /**
     * @param CollectionInterface $other
     */
    public function diff($other) : CollectionInterface
    {
        $this->compareCollectionTypes($other);
        $diffAtoB = array_udiff($this->data, $other->toArray(), $this->getComparator());
        $diffBtoA = array_udiff($other->toArray(), $this->data, $this->getComparator());
        $diff = array_merge($diffAtoB, $diffBtoA);
        $collection = clone $this;
        $collection->data = $diff;
        return $collection;
    }
    /**
     * @param CollectionInterface $other
     */
    public function intersect($other) : CollectionInterface
    {
        $this->compareCollectionTypes($other);
        $intersect = array_uintersect($this->data, $other->toArray(), $this->getComparator());
        $collection = clone $this;
        $collection->data = $intersect;
        return $collection;
    }
    /**
     * @param CollectionInterface ...$collections
     */
    public function merge(...$collections) : CollectionInterface
    {
        $mergedCollection = clone $this;
        foreach ($collections as $index => $collection) {
            if (!$collection instanceof static) {
                throw new CollectionMismatchException(sprintf('Collection with index %d must be of type %s', $index, static::class));
            }
            if ($collection->getType() !== $this->getType()) {
                throw new CollectionMismatchException(sprintf('Collection items in collection with index %d must be of type %s', $index, $this->getType()));
            }
            foreach ($collection as $key => $value) {
                if (is_int($key)) {
                    $mergedCollection[] = $value;
                } else {
                    $mergedCollection[$key] = $value;
                }
            }
        }
        return $mergedCollection;
    }
    /**
     * @return void
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized, ['allowed_classes' => [$this->getType()]]);
        $this->data = $data;
    }
    /**
     * @return void
     */
    private function compareCollectionTypes(CollectionInterface $other)
    {
        if (!$other instanceof static) {
            throw new CollectionMismatchException('Collection must be of type ' . static::class);
        }
        if ($other->getType() !== $this->getType()) {
            throw new CollectionMismatchException('Collection items must be of type ' . $this->getType());
        }
    }
    private function getComparator() : Closure
    {
        return function ($a, $b) : int {
            if (\is_object($a) && \is_object($b)) {
                $a = \spl_object_id($a);
                $b = \spl_object_id($b);
            }
            return $a === $b ? 0 : ($a < $b ? 1 : -1);
        };
    }
}
