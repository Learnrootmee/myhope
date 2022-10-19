<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Parser;

class Token
{
    const TYPE_FILE_END = 'eof';
    const TYPE_DELIMITER = 'delimiter';
    const TYPE_WHITESPACE = 'whitespace';
    const TYPE_IDENTIFIER = 'identifier';
    const TYPE_HASH = 'hash';
    const TYPE_NUMBER = 'number';
    const TYPE_STRING = 'string';
    /**
     * @var string|null
     */
    private $type;
    /**
     * @var string|null
     */
    private $value;
    /**
     * @var int|null
     */
    private $position;
    /**
     * @param string|null $type
     * @param string|null $value
     * @param int|null $position
     */
    public function __construct($type, $value, $position)
    {
        $this->type = $type;
        $this->value = $value;
        $this->position = $position;
    }
    /**
     * @return int|null
     */
    public function getType()
    {
        return $this->type;
    }
    /**
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }
    public function isFileEnd() : bool
    {
        return self::TYPE_FILE_END === $this->type;
    }
    /**
     * @param mixed[] $values
     */
    public function isDelimiter($values = []) : bool
    {
        if (self::TYPE_DELIMITER !== $this->type) {
            return \false;
        }
        if (empty($values)) {
            return \true;
        }
        return \in_array($this->value, $values);
    }
    public function isWhitespace() : bool
    {
        return self::TYPE_WHITESPACE === $this->type;
    }
    public function isIdentifier() : bool
    {
        return self::TYPE_IDENTIFIER === $this->type;
    }
    public function isHash() : bool
    {
        return self::TYPE_HASH === $this->type;
    }
    public function isNumber() : bool
    {
        return self::TYPE_NUMBER === $this->type;
    }
    public function isString() : bool
    {
        return self::TYPE_STRING === $this->type;
    }
    public function __toString() : string
    {
        if ($this->value) {
            return \sprintf('<%s "%s" at %s>', $this->type, $this->value, $this->position);
        }
        return \sprintf('<%s at %s>', $this->type, $this->position);
    }
}
