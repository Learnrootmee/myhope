<?php

namespace Staatic\Vendor\AsyncAws\Core\Sts\ValueObject;

final class AssumedRoleUser
{
    private $assumedRoleId;
    private $arn;
    public function __construct(array $input)
    {
        $this->assumedRoleId = $input['AssumedRoleId'] ?? null;
        $this->arn = $input['Arn'] ?? null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    public function getArn() : string
    {
        return $this->arn;
    }
    public function getAssumedRoleId() : string
    {
        return $this->assumedRoleId;
    }
}
