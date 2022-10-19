<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Map;

use Staatic\Vendor\Ramsey\Collection\Exception\InvalidArgumentException;
use Staatic\Vendor\Ramsey\Collection\Tool\TypeTrait;
use Staatic\Vendor\Ramsey\Collection\Tool\ValueToStringTrait;
abstract class AbstractTypedMap extends AbstractMap implements TypedMapInterface
{
    use TypeTrait;
    use ValueToStringTrait;
    /**
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === null) {
            throw new InvalidArgumentException('Map elements are key/value pairs; a key must be provided for ' . 'value ' . \var_export($value, \true));
        }
        if ($this->checkType($this->getKeyType(), $offset) === \false) {
            throw new InvalidArgumentException('Key must be of type ' . $this->getKeyType() . '; key is ' . $this->toolValueToString($offset));
        }
        if ($this->checkType($this->getValueType(), $value) === \false) {
            throw new InvalidArgumentException('Value must be of type ' . $this->getValueType() . '; value is ' . $this->toolValueToString($value));
        }
        parent::offsetSet($offset, $value);
    }
}
