<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DateTimeImmutable;
final class Part
{
    private $partNumber;
    private $lastModified;
    private $etag;
    private $size;
    private $checksumCrc32;
    private $checksumCrc32C;
    private $checksumSha1;
    private $checksumSha256;
    public function __construct(array $input)
    {
        $this->partNumber = $input['PartNumber'] ?? null;
        $this->lastModified = $input['LastModified'] ?? null;
        $this->etag = $input['ETag'] ?? null;
        $this->size = $input['Size'] ?? null;
        $this->checksumCrc32 = $input['ChecksumCRC32'] ?? null;
        $this->checksumCrc32C = $input['ChecksumCRC32C'] ?? null;
        $this->checksumSha1 = $input['ChecksumSHA1'] ?? null;
        $this->checksumSha256 = $input['ChecksumSHA256'] ?? null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    /**
     * @return string|null
     */
    public function getChecksumCrc32()
    {
        return $this->checksumCrc32;
    }
    /**
     * @return string|null
     */
    public function getChecksumCrc32C()
    {
        return $this->checksumCrc32C;
    }
    /**
     * @return string|null
     */
    public function getChecksumSha1()
    {
        return $this->checksumSha1;
    }
    /**
     * @return string|null
     */
    public function getChecksumSha256()
    {
        return $this->checksumSha256;
    }
    /**
     * @return string|null
     */
    public function getEtag()
    {
        return $this->etag;
    }
    /**
     * @return DateTimeImmutable|null
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }
    /**
     * @return int|null
     */
    public function getPartNumber()
    {
        return $this->partNumber;
    }
    /**
     * @return string|null
     */
    public function getSize()
    {
        return $this->size;
    }
}
