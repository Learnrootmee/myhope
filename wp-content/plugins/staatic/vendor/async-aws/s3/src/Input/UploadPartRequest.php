<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestPayer;
final class UploadPartRequest extends Input
{
    private $body;
    private $bucket;
    private $contentLength;
    private $contentMd5;
    private $checksumAlgorithm;
    private $checksumCrc32;
    private $checksumCrc32C;
    private $checksumSha1;
    private $checksumSha256;
    private $key;
    private $partNumber;
    private $uploadId;
    private $sseCustomerAlgorithm;
    private $sseCustomerKey;
    private $sseCustomerKeyMd5;
    private $requestPayer;
    private $expectedBucketOwner;
    public function __construct(array $input = [])
    {
        $this->body = $input['Body'] ?? null;
        $this->bucket = $input['Bucket'] ?? null;
        $this->contentLength = $input['ContentLength'] ?? null;
        $this->contentMd5 = $input['ContentMD5'] ?? null;
        $this->checksumAlgorithm = $input['ChecksumAlgorithm'] ?? null;
        $this->checksumCrc32 = $input['ChecksumCRC32'] ?? null;
        $this->checksumCrc32C = $input['ChecksumCRC32C'] ?? null;
        $this->checksumSha1 = $input['ChecksumSHA1'] ?? null;
        $this->checksumSha256 = $input['ChecksumSHA256'] ?? null;
        $this->key = $input['Key'] ?? null;
        $this->partNumber = $input['PartNumber'] ?? null;
        $this->uploadId = $input['UploadId'] ?? null;
        $this->sseCustomerAlgorithm = $input['SSECustomerAlgorithm'] ?? null;
        $this->sseCustomerKey = $input['SSECustomerKey'] ?? null;
        $this->sseCustomerKeyMd5 = $input['SSECustomerKeyMD5'] ?? null;
        $this->requestPayer = $input['RequestPayer'] ?? null;
        $this->expectedBucketOwner = $input['ExpectedBucketOwner'] ?? null;
        parent::__construct($input);
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    public function getBody()
    {
        return $this->body;
    }
    /**
     * @return string|null
     */
    public function getBucket()
    {
        return $this->bucket;
    }
    /**
     * @return string|null
     */
    public function getChecksumAlgorithm()
    {
        return $this->checksumAlgorithm;
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
    public function getContentLength()
    {
        return $this->contentLength;
    }
    /**
     * @return string|null
     */
    public function getContentMd5()
    {
        return $this->contentMd5;
    }
    /**
     * @return string|null
     */
    public function getExpectedBucketOwner()
    {
        return $this->expectedBucketOwner;
    }
    /**
     * @return string|null
     */
    public function getKey()
    {
        return $this->key;
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
    public function getRequestPayer()
    {
        return $this->requestPayer;
    }
    /**
     * @return string|null
     */
    public function getSseCustomerAlgorithm()
    {
        return $this->sseCustomerAlgorithm;
    }
    /**
     * @return string|null
     */
    public function getSseCustomerKey()
    {
        return $this->sseCustomerKey;
    }
    /**
     * @return string|null
     */
    public function getSseCustomerKeyMd5()
    {
        return $this->sseCustomerKeyMd5;
    }
    /**
     * @return string|null
     */
    public function getUploadId()
    {
        return $this->uploadId;
    }
    public function request() : Request
    {
        $headers = [];
        if (null !== $this->contentLength) {
            $headers['Content-Length'] = $this->contentLength;
        }
        if (null !== $this->contentMd5) {
            $headers['Content-MD5'] = $this->contentMd5;
        }
        if (null !== $this->checksumAlgorithm) {
            if (!ChecksumAlgorithm::exists($this->checksumAlgorithm)) {
                throw new InvalidArgument(\sprintf('Invalid parameter "ChecksumAlgorithm" for "%s". The value "%s" is not a valid "ChecksumAlgorithm".', __CLASS__, $this->checksumAlgorithm));
            }
            $headers['x-amz-sdk-checksum-algorithm'] = $this->checksumAlgorithm;
        }
        if (null !== $this->checksumCrc32) {
            $headers['x-amz-checksum-crc32'] = $this->checksumCrc32;
        }
        if (null !== $this->checksumCrc32C) {
            $headers['x-amz-checksum-crc32c'] = $this->checksumCrc32C;
        }
        if (null !== $this->checksumSha1) {
            $headers['x-amz-checksum-sha1'] = $this->checksumSha1;
        }
        if (null !== $this->checksumSha256) {
            $headers['x-amz-checksum-sha256'] = $this->checksumSha256;
        }
        if (null !== $this->sseCustomerAlgorithm) {
            $headers['x-amz-server-side-encryption-customer-algorithm'] = $this->sseCustomerAlgorithm;
        }
        if (null !== $this->sseCustomerKey) {
            $headers['x-amz-server-side-encryption-customer-key'] = $this->sseCustomerKey;
        }
        if (null !== $this->sseCustomerKeyMd5) {
            $headers['x-amz-server-side-encryption-customer-key-MD5'] = $this->sseCustomerKeyMd5;
        }
        if (null !== $this->requestPayer) {
            if (!RequestPayer::exists($this->requestPayer)) {
                throw new InvalidArgument(\sprintf('Invalid parameter "RequestPayer" for "%s". The value "%s" is not a valid "RequestPayer".', __CLASS__, $this->requestPayer));
            }
            $headers['x-amz-request-payer'] = $this->requestPayer;
        }
        if (null !== $this->expectedBucketOwner) {
            $headers['x-amz-expected-bucket-owner'] = $this->expectedBucketOwner;
        }
        $query = [];
        if (null === ($v = $this->partNumber)) {
            throw new InvalidArgument(\sprintf('Missing parameter "PartNumber" for "%s". The value cannot be null.', __CLASS__));
        }
        $query['partNumber'] = (string) $v;
        if (null === ($v = $this->uploadId)) {
            throw new InvalidArgument(\sprintf('Missing parameter "UploadId" for "%s". The value cannot be null.', __CLASS__));
        }
        $query['uploadId'] = $v;
        $uri = [];
        if (null === ($v = $this->bucket)) {
            throw new InvalidArgument(\sprintf('Missing parameter "Bucket" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Bucket'] = $v;
        if (null === ($v = $this->key)) {
            throw new InvalidArgument(\sprintf('Missing parameter "Key" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Key'] = $v;
        $uriString = '/' . \rawurlencode($uri['Bucket']) . '/' . \str_replace('%2F', '/', \rawurlencode($uri['Key']));
        $body = $this->body ?? '';
        return new Request('PUT', $uriString, $query, $headers, StreamFactory::create($body));
    }
    public function setBody($value) : self
    {
        $this->body = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setBucket($value) : self
    {
        $this->bucket = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setChecksumAlgorithm($value) : self
    {
        $this->checksumAlgorithm = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setChecksumCrc32($value) : self
    {
        $this->checksumCrc32 = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setChecksumCrc32C($value) : self
    {
        $this->checksumCrc32C = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setChecksumSha1($value) : self
    {
        $this->checksumSha1 = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setChecksumSha256($value) : self
    {
        $this->checksumSha256 = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setContentLength($value) : self
    {
        $this->contentLength = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setContentMd5($value) : self
    {
        $this->contentMd5 = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setExpectedBucketOwner($value) : self
    {
        $this->expectedBucketOwner = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setKey($value) : self
    {
        $this->key = $value;
        return $this;
    }
    /**
     * @param int|null $value
     */
    public function setPartNumber($value) : self
    {
        $this->partNumber = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setRequestPayer($value) : self
    {
        $this->requestPayer = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setSseCustomerAlgorithm($value) : self
    {
        $this->sseCustomerAlgorithm = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setSseCustomerKey($value) : self
    {
        $this->sseCustomerKey = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setSseCustomerKeyMd5($value) : self
    {
        $this->sseCustomerKeyMd5 = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setUploadId($value) : self
    {
        $this->uploadId = $value;
        return $this;
    }
}
