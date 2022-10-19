<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Map;

use Staatic\Vendor\Ramsey\Collection\ArrayInterface;
interface MapInterface extends ArrayInterface
{
    public function containsKey($key) : bool;
    public function containsValue($value) : bool;
    public function keys() : array;
    public function get($key, $defaultValue = null);
    public function put($key, $value);
    public function putIfAbsent($key, $value);
    public function remove($key);
    public function removeIf($key, $value) : bool;
    public function replace($key, $value);
    public function replaceIf($key, $oldValue, $newValue) : bool;
}
