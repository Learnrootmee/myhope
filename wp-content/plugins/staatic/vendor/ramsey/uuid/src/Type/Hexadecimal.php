<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Type;

use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidArgumentException;
use ValueError;
use function ctype_xdigit;
use function sprintf;
use function str_starts_with;
use function strtolower;
use function substr;
final class Hexadecimal implements TypeInterface
{
    /**
     * @var string
     */
    private $value;
    public function __construct(string $value)
    {
        $value = strtolower($value);
        if (strncmp($value, '0x', strlen('0x')) === 0) {
            $value = substr($value, 2);
        }
        if (!ctype_xdigit($value)) {
            throw new InvalidArgumentException('Value must be a hexadecimal number');
        }
        $this->value = $value;
    }
    public function toString() : string
    {
        return $this->value;
    }
    public function __toString() : string
    {
        return $this->toString();
    }
    public function jsonSerialize() : string
    {
        return $this->toString();
    }
    public function serialize() : string
    {
        return $this->toString();
    }
    public function __serialize() : array
    {
        return ['string' => $this->toString()];
    }
    /**
     * @param string $data
     * @return void
     */
    public function unserialize($data)
    {
        $this->__construct($data);
    }
    /**
     * @param mixed[] $data
     * @return void
     */
    public function __unserialize($data)
    {
        if (!isset($data['string'])) {
            throw new ValueError(sprintf('%s(): Argument #1 ($data) is invalid', __METHOD__));
        }
        $this->unserialize($data['string']);
    }
}
