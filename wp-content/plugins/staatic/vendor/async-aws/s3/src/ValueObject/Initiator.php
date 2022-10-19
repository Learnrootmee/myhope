<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

final class Initiator
{
    private $id;
    private $displayName;
    public function __construct(array $input)
    {
        $this->id = $input['ID'] ?? null;
        $this->displayName = $input['DisplayName'] ?? null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    /**
     * @return string|null
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }
    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }
}
