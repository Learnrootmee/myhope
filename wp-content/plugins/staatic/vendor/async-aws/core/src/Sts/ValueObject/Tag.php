<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\ValueObject;

use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class Tag
{
    private $key;
    private $value;
    public function __construct(array $input)
    {
        $this->key = $input['Key'] ?? null;
        $this->value = $input['Value'] ?? null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    public function getKey() : string
    {
        return $this->key;
    }
    public function getValue() : string
    {
        return $this->value;
    }
    public function requestBody() : array
    {
        $payload = [];
        if (null === ($v = $this->key)) {
            throw new InvalidArgument(\sprintf('Missing parameter "Key" for "%s". The value cannot be null.', __CLASS__));
        }
        $payload['Key'] = $v;
        if (null === ($v = $this->value)) {
            throw new InvalidArgument(\sprintf('Missing parameter "Value" for "%s". The value cannot be null.', __CLASS__));
        }
        $payload['Value'] = $v;
        return $payload;
    }
}
