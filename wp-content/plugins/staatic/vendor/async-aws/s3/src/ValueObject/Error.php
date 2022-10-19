<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

final class Error
{
    private $key;
    private $versionId;
    private $code;
    private $message;
    public function __construct(array $input)
    {
        $this->key = $input['Key'] ?? null;
        $this->versionId = $input['VersionId'] ?? null;
        $this->code = $input['Code'] ?? null;
        $this->message = $input['Message'] ?? null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    /**
     * @return string|null
     */
    public function getCode()
    {
        return $this->code;
    }
    /**
     * @return string|null
     */
    public function getKey()
    {
        return $this->key;
    }
    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }
    /**
     * @return string|null
     */
    public function getVersionId()
    {
        return $this->versionId;
    }
}
