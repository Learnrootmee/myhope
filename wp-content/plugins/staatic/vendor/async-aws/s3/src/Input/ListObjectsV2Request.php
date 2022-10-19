<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\EncodingType;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestPayer;
final class ListObjectsV2Request extends Input
{
    private $bucket;
    private $delimiter;
    private $encodingType;
    private $maxKeys;
    private $prefix;
    private $continuationToken;
    private $fetchOwner;
    private $startAfter;
    private $requestPayer;
    private $expectedBucketOwner;
    public function __construct(array $input = [])
    {
        $this->bucket = $input['Bucket'] ?? null;
        $this->delimiter = $input['Delimiter'] ?? null;
        $this->encodingType = $input['EncodingType'] ?? null;
        $this->maxKeys = $input['MaxKeys'] ?? null;
        $this->prefix = $input['Prefix'] ?? null;
        $this->continuationToken = $input['ContinuationToken'] ?? null;
        $this->fetchOwner = $input['FetchOwner'] ?? null;
        $this->startAfter = $input['StartAfter'] ?? null;
        $this->requestPayer = $input['RequestPayer'] ?? null;
        $this->expectedBucketOwner = $input['ExpectedBucketOwner'] ?? null;
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
    public function getContinuationToken()
    {
        return $this->continuationToken;
    }
    /**
     * @return string|null
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }
    /**
     * @return string|null
     */
    public function getEncodingType()
    {
        return $this->encodingType;
    }
    /**
     * @return string|null
     */
    public function getExpectedBucketOwner()
    {
        return $this->expectedBucketOwner;
    }
    /**
     * @return bool|null
     */
    public function getFetchOwner()
    {
        return $this->fetchOwner;
    }
    /**
     * @return int|null
     */
    public function getMaxKeys()
    {
        return $this->maxKeys;
    }
    /**
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->prefix;
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
    public function getStartAfter()
    {
        return $this->startAfter;
    }
    public function request() : Request
    {
        $headers = ['content-type' => 'application/xml'];
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
        if (null !== $this->delimiter) {
            $query['delimiter'] = $this->delimiter;
        }
        if (null !== $this->encodingType) {
            if (!EncodingType::exists($this->encodingType)) {
                throw new InvalidArgument(\sprintf('Invalid parameter "EncodingType" for "%s". The value "%s" is not a valid "EncodingType".', __CLASS__, $this->encodingType));
            }
            $query['encoding-type'] = $this->encodingType;
        }
        if (null !== $this->maxKeys) {
            $query['max-keys'] = (string) $this->maxKeys;
        }
        if (null !== $this->prefix) {
            $query['prefix'] = $this->prefix;
        }
        if (null !== $this->continuationToken) {
            $query['continuation-token'] = $this->continuationToken;
        }
        if (null !== $this->fetchOwner) {
            $query['fetch-owner'] = $this->fetchOwner ? 'true' : 'false';
        }
        if (null !== $this->startAfter) {
            $query['start-after'] = $this->startAfter;
        }
        $uri = [];
        if (null === ($v = $this->bucket)) {
            throw new InvalidArgument(\sprintf('Missing parameter "Bucket" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Bucket'] = $v;
        $uriString = '/' . \rawurlencode($uri['Bucket']) . '?list-type=2';
        $body = '';
        return new Request('GET', $uriString, $query, $headers, StreamFactory::create($body));
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
    public function setContinuationToken($value) : self
    {
        $this->continuationToken = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setDelimiter($value) : self
    {
        $this->delimiter = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setEncodingType($value) : self
    {
        $this->encodingType = $value;
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
     * @param bool|null $value
     */
    public function setFetchOwner($value) : self
    {
        $this->fetchOwner = $value;
        return $this;
    }
    /**
     * @param int|null $value
     */
    public function setMaxKeys($value) : self
    {
        $this->maxKeys = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setPrefix($value) : self
    {
        $this->prefix = $value;
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
    public function setStartAfter($value) : self
    {
        $this->startAfter = $value;
        return $this;
    }
}
