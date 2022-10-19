<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

final class ServerSideEncryptionRule
{
    private $applyServerSideEncryptionByDefault;
    private $bucketKeyEnabled;
    public function __construct(array $input)
    {
        $this->applyServerSideEncryptionByDefault = isset($input['ApplyServerSideEncryptionByDefault']) ? ServerSideEncryptionByDefault::create($input['ApplyServerSideEncryptionByDefault']) : null;
        $this->bucketKeyEnabled = $input['BucketKeyEnabled'] ?? null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    /**
     * @return ServerSideEncryptionByDefault|null
     */
    public function getApplyServerSideEncryptionByDefault()
    {
        return $this->applyServerSideEncryptionByDefault;
    }
    /**
     * @return bool|null
     */
    public function getBucketKeyEnabled()
    {
        return $this->bucketKeyEnabled;
    }
}
