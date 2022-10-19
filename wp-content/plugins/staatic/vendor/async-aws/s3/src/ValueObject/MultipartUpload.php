<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\StorageClass;
final class MultipartUpload
{
    private $uploadId;
    private $key;
    private $initiated;
    private $storageClass;
    private $owner;
    private $initiator;
    private $checksumAlgorithm;
    public function __construct(array $input)
    {
        $this->uploadId = $input['UploadId'] ?? null;
        $this->key = $input['Key'] ?? null;
        $this->initiated = $input['Initiated'] ?? null;
        $this->storageClass = $input['StorageClass'] ?? null;
        $this->owner = isset($input['Owner']) ? Owner::create($input['Owner']) : null;
        $this->initiator = isset($input['Initiator']) ? Initiator::create($input['Initiator']) : null;
        $this->checksumAlgorithm = $input['ChecksumAlgorithm'] ?? null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    /**
     * @return string|null
     */
    public function getChecksumAlgorithm()
    {
        return $this->checksumAlgorithm;
    }
    /**
     * @return DateTimeImmutable|null
     */
    public function getInitiated()
    {
        return $this->initiated;
    }
    /**
     * @return Initiator|null
     */
    public function getInitiator()
    {
        return $this->initiator;
    }
    /**
     * @return string|null
     */
    public function getKey()
    {
        return $this->key;
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
    public function getStorageClass()
    {
        return $this->storageClass;
    }
    /**
     * @return string|null
     */
    public function getUploadId()
    {
        return $this->uploadId;
    }
}
