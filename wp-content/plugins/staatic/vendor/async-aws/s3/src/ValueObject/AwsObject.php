<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectStorageClass;
final class AwsObject
{
    private $key;
    private $lastModified;
    private $etag;
    private $checksumAlgorithm;
    private $size;
    private $storageClass;
    private $owner;
    public function __construct(array $input)
    {
        $this->key = $input['Key'] ?? null;
        $this->lastModified = $input['LastModified'] ?? null;
        $this->etag = $input['ETag'] ?? null;
        $this->checksumAlgorithm = $input['ChecksumAlgorithm'] ?? null;
        $this->size = $input['Size'] ?? null;
        $this->storageClass = $input['StorageClass'] ?? null;
        $this->owner = isset($input['Owner']) ? Owner::create($input['Owner']) : null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    public function getChecksumAlgorithm() : array
    {
        return $this->checksumAlgorithm ?? [];
    }
    /**
     * @return string|null
     */
    public function getEtag()
    {
        return $this->etag;
    }
    /**
     * @return string|null
     */
    public function getKey()
    {
        return $this->key;
    }
    /**
     * @return DateTimeImmutable|null
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }
    /**
     * @return Owner|null
     */
    public function getOwner()
    {
        return $this->owner;
    }
    /**
     * @return string|null
     */
    public function getSize()
    {
        return $this->size;
    }
    /**
     * @return string|null
     */
    public function getStorageClass()
    {
        return $this->storageClass;
    }
}
