<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Type;

use Staatic\Vendor\Ramsey\Uuid\Exception\InvalidArgumentException;
use ValueError;
use function ctype_digit;
use function ltrim;
use function sprintf;
use function str_starts_with;
use function substr;
final class Integer implements NumberInterface
{
    /**
     * @var string
     */
    private $value;
    /**
     * @var bool
     */
    private $isNegative = \false;
    /**
     * @param float|int|string|$this $value
     */
    public function __construct($value)
    {
        $value = (string) $value;
        $sign = '+';
        if (strncmp($value, '-', strlen('-')) === 0 || strncmp($value, '+', strlen('+')) === 0) {
            $sign = substr($value, 0, 1);
            $value = substr($value, 1);
        }
        if (!ctype_digit($value)) {
            throw new InvalidArgumentException('Value must be a signed integer or a string containing only ' . 'digits 0-9 and, optionally, a sign (+ or -)');
        }
        $value = ltrim($value, '0');
        if ($value === '') {
            $value = '0';
        }
        if ($sign === '-' && $value !== '0') {
            $value = $sign . $value;
            $this->isNegative = \true;
        }
        $numericValue = $value;
        $this->value = $numericValue;
    }
    public function isNegative() : bool
    {
        return $this->isNegative;
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
