<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DateTimeImmutable;
final class Bucket
{
    private $name;
    private $creationDate;
    public function __construct(array $input)
    {
        $this->name = $input['Name'] ?? null;
        $this->creationDate = $input['CreationDate'] ?? null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    /**
     * @return DateTimeImmutable|null
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }
}
