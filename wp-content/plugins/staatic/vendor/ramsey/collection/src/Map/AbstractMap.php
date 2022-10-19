<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Map;

use Staatic\Vendor\Ramsey\Collection\AbstractArray;
use Staatic\Vendor\Ramsey\Collection\Exception\InvalidArgumentException;
use function array_key_exists;
use function array_keys;
use function in_array;
abstract class AbstractMap extends AbstractArray implements MapInterface
{
    /**
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            throw new InvalidArgumentException('Map elements are key/value pairs; a key must be provided for ' . 'value ' . \var_export($value, \true));
        }
        $this->data[$offset] = $value;
    }
    public function containsKey($key) : bool
    {
        return array_key_exists($key, $this->data);
    }
    public function containsValue($value) : bool
    {
        return in_array($value, $this->data, \true);
    }
    public function keys() : array
    {
        return array_keys($this->data);
    }
    public function get($key, $defaultValue = null)
    {
        if (!$this->containsKey($key)) {
            return $defaultValue;
        }
        return $this[$key];
    }
    public function put($key, $value)
    {
        $previousValue = $this->get($key);
        $this[$key] = $value;
        return $previousValue;
    }
    public function putIfAbsent($key, $value)
    {
        $currentValue = $this->get($key);
        if ($currentValue === null) {
            $this[$key] = $value;
        }
        return $currentValue;
    }
    public function remove($key)
    {
        $previousValue = $this->get($key);
        unset($this[$key]);
        return $previousValue;
    }
    public function removeIf($key, $value) : bool
    {
        if ($this->get($key) === $value) {
            unset($this[$key]);
            return \true;
        }
        return \false;
    }
    public function replace($key, $value)
    {
        $currentValue = $this->get($key);
        if ($this->containsKey($key)) {
            $this[$key] = $value;
        }
        return $currentValue;
    }
    public function replaceIf($key, $oldValue, $newValue) : bool
    {
        if ($this->get($key) === $oldValue) {
            $this[$key] = $newValue;
            return \true;
        }
        return \false;
    }
}
