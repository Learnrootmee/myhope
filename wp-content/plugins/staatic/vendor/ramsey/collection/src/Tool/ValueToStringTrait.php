<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Tool;

use DateTimeInterface;
use function get_class;
use function get_resource_type;
use function is_array;
use function is_bool;
use function is_callable;
use function is_resource;
use function is_scalar;
trait ValueToStringTrait
{
    protected function toolValueToString($value) : string
    {
        if ($value === null) {
            return 'NULL';
        }
        if (is_bool($value)) {
            return $value ? 'TRUE' : 'FALSE';
        }
        if (is_array($value)) {
            return 'Array';
        }
        if (is_scalar($value)) {
            return (string) $value;
        }
        if (is_resource($value)) {
            return '(' . get_resource_type($value) . ' resource #' . (int) $value . ')';
        }
        if (!\is_object($value)) {
            return '(' . \var_export($value, \true) . ')';
        }
        if (is_callable([$value, '__toString'])) {
            return (string) $value->__toString();
        }
        if ($value instanceof DateTimeInterface) {
            return $value->format('c');
        }
        return '(' . get_class($value) . ' Object)';
    }
}
