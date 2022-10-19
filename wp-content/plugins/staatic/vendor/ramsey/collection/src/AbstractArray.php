<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection;

use ReturnTypeWillChange;
use ArrayIterator;
use Traversable;
use function serialize;
use function unserialize;
abstract class AbstractArray implements ArrayInterface
{
    protected $data = [];
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this[$key] = $value;
        }
    }
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->data);
    }
    public function offsetExists($offset) : bool
    {
        return isset($this->data[$offset]);
    }
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }
    /**
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }
    /**
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
    public function serialize() : string
    {
        return serialize($this->data);
    }
    public function __serialize() : array
    {
        return $this->data;
    }
    /**
     * @return void
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized, ['allowed_classes' => \false]);
        $this->data = $data;
    }
    /**
     * @param mixed[] $data
     * @return void
     */
    public function __unserialize($data)
    {
        $this->data = $data;
    }
    public function count() : int
    {
        return \count($this->data);
    }
    /**
     * @return void
     */
    public function clear()
    {
        $this->data = [];
    }
    public function toArray() : array
    {
        return $this->data;
    }
    public function isEmpty() : bool
    {
        return \count($this->data) === 0;
    }
}
