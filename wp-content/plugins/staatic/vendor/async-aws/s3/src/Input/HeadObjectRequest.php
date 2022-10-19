<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use DateTimeImmutable;
use DateTimeZone;
use DateTimeInterface;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumMode;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestPayer;
final class HeadObjectRequest extends Input
{
    private $bucket;
    private $ifMatch;
    private $ifModifiedSince;
    private $ifNoneMatch;
    private $ifUnmodifiedSince;
    private $key;
    private $range;
    private $versionId;
    private $sseCustomerAlgorithm;
    private $sseCustomerKey;
    private $sseCustomerKeyMd5;
    private $requestPayer;
    private $partNumber;
    private $expectedBucketOwner;
    private $checksumMode;
    public function __construct(array $input = [])
    {
        $this->bucket = $input['Bucket'] ?? null;
        $this->ifMatch = $input['IfMatch'] ?? null;
        $this->ifModifiedSince = !isset($input['IfModifiedSince']) ? null : ($input['IfModifiedSince'] instanceof DateTimeImmutable ? $input['IfModifiedSince'] : new DateTimeImmutable($input['IfModifiedSince']));
        $this->ifNoneMatch = $input['IfNoneMatch'] ?? null;
        $this->ifUnmodifiedSince = !isset($input['IfUnmodifiedSince']) ? null : ($input['IfUnmodifiedSince'] instanceof DateTimeImmutable ? $input['IfUnmodifiedSince'] : new DateTimeImmutable($input['IfUnmodifiedSince']));
        $this->key = $input['Key'] ?? null;
        $this->range = $input['Range'] ?? null;
        $this->versionId = $input['VersionId'] ?? null;
        $this->sseCustomerAlgorithm = $input['SSECustomerAlgorithm'] ?? null;
        $this->sseCustomerKey = $input['SSECustomerKey'] ?? null;
        $this->sseCustomerKeyMd5 = $input['SSECustomerKeyMD5'] ?? null;
        $this->requestPayer = $input['RequestPayer'] ?? null;
        $this->partNumber = $input['PartNumber'] ?? null;
        $this->expectedBucketOwner = $input['ExpectedBucketOwner'] ?? null;
        $this->checksumMode = $input['ChecksumMode'] ?? null;
        parent::__construct($input);
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
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
    public function getChecksumMode()
    {
        return $this->checksumMode;
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
    public function getIfMatch()
    {
        return $this->ifMatch;
    }
    /**
     * @return DateTimeImmutable|null
     */
    public function getIfModifiedSince()
    {
        return $this->ifModifiedSince;
    }
    /**
     * @return string|null
     */
    public function getIfNoneMatch()
    {
        return $this->ifNoneMatch;
    }
    /**
     * @return DateTimeImmutable|null
     */
    public function getIfUnmodifiedSince()
    {
        return $this->ifUnmodifiedSince;
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
    public function getRange()
    {
        return $this->range;
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
    public function getVersionId()
    {
        return $this->versionId;
    }
    public function request() : Request
    {
        $headers = ['content-type' => 'application/xml'];
        if (null !== $this->ifMatch) {
            $headers['If-Match'] = $this->ifMatch;
        }
        if (null !== $this->ifModifiedSince) {
            $headers['If-Modified-Since'] = $this->ifModifiedSince->setTimezone(new DateTimeZone('GMT'))->format(DateTimeInterface::RFC7231);
        }
        if (null !== $this->ifNoneMatch) {
            $headers['If-None-Match'] = $this->ifNoneMatch;
        }
        if (null !== $this->ifUnmodifiedSince) {
            $headers['If-Unmodified-Since'] = $this->ifUnmodifiedSince->setTimezone(new DateTimeZone('GMT'))->format(DateTimeInterface::RFC7231);
        }
        if (null !== $this->range) {
            $headers['Range'] = $this->range;
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
        if (null !== $this->checksumMode) {
            if (!ChecksumMode::exists($this->checksumMode)) {
                throw new InvalidArgument(\sprintf('Invalid parameter "ChecksumMode" for "%s". The value "%s" is not a valid "ChecksumMode".', __CLASS__, $this->checksumMode));
            }
            $headers['x-amz-checksum-mode'] = $this->checksumMode;
        }
        $query = [];
        if (null !== $this->versionId) {
            $query['versionId'] = $this->versionId;
        }
        if (null !== $this->partNumber) {
            $query['partNumber'] = (string) $this->partNumber;
        }
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
        $body = '';
        return new Request('HEAD', $uriString, $query, $headers, StreamFactory::create($body));
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
    public function setChecksumMode($value) : self
    {
        $this->checksumMode = $value;
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
    public function setIfMatch($value) : self
    {
        $this->ifMatch = $value;
        return $this;
    }
    /**
     * @param DateTimeImmutable|null $value
     */
    public function setIfModifiedSince($value) : self
    {
        $this->ifModifiedSince = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setIfNoneMatch($value) : self
    {
        $this->ifNoneMatch = $value;
        return $this;
    }
    /**
     * @param DateTimeImmutable|null $value
     */
    public function setIfUnmodifiedSince($value) : self
    {
        $this->ifUnmodifiedSince = $value;
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
    public function setRange($value) : self
    {
        $this->range = $value;
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
    public function setVersionId($value) : self
    {
        $this->versionId = $value;
        return $this;
    }
}
