<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

use DOMElement;
use DOMDocument;
final class CompletedPart
{
    private $etag;
    private $checksumCrc32;
    private $checksumCrc32C;
    private $checksumSha1;
    private $checksumSha256;
    private $partNumber;
    public function __construct(array $input)
    {
        $this->etag = $input['ETag'] ?? null;
        $this->checksumCrc32 = $input['ChecksumCRC32'] ?? null;
        $this->checksumCrc32C = $input['ChecksumCRC32C'] ?? null;
        $this->checksumSha1 = $input['ChecksumSHA1'] ?? null;
        $this->checksumSha256 = $input['ChecksumSHA256'] ?? null;
        $this->partNumber = $input['PartNumber'] ?? null;
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
     * @return int|null
     */
    public function getPartNumber()
    {
        return $this->partNumber;
    }
    /**
     * @return void
     */
    public function requestBody(DOMElement $node, DOMDocument $document)
    {
        if (null !== ($v = $this->etag)) {
            $node->appendChild($document->createElement('ETag', $v));
        }
        if (null !== ($v = $this->checksumCrc32)) {
            $node->appendChild($document->createElement('ChecksumCRC32', $v));
        }
        if (null !== ($v = $this->checksumCrc32C)) {
            $node->appendChild($document->createElement('ChecksumCRC32C', $v));
        }
        if (null !== ($v = $this->checksumSha1)) {
            $node->appendChild($document->createElement('ChecksumSHA1', $v));
        }
        if (null !== ($v = $this->checksumSha256)) {
            $node->appendChild($document->createElement('ChecksumSHA256', $v));
        }
        if (null !== ($v = $this->partNumber)) {
            $node->appendChild($document->createElement('PartNumber', $v));
        }
    }
}
