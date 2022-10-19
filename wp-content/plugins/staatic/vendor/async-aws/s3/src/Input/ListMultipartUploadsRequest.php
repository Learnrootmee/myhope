<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\EncodingType;
final class ListMultipartUploadsRequest extends Input
{
    private $bucket;
    private $delimiter;
    private $encodingType;
    private $keyMarker;
    private $maxUploads;
    private $prefix;
    private $uploadIdMarker;
    private $expectedBucketOwner;
    public function __construct(array $input = [])
    {
        $this->bucket = $input['Bucket'] ?? null;
        $this->delimiter = $input['Delimiter'] ?? null;
        $this->encodingType = $input['EncodingType'] ?? null;
        $this->keyMarker = $input['KeyMarker'] ?? null;
        $this->maxUploads = $input['MaxUploads'] ?? null;
        $this->prefix = $input['Prefix'] ?? null;
        $this->uploadIdMarker = $input['UploadIdMarker'] ?? null;
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
     * @return string|null
     */
    public function getKeyMarker()
    {
        return $this->keyMarker;
    }
    /**
     * @return int|null
     */
    public function getMaxUploads()
    {
        return $this->maxUploads;
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
    public function getUploadIdMarker()
    {
        return $this->uploadIdMarker;
    }
    public function request() : Request
    {
        $headers = ['content-type' => 'application/xml'];
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
        if (null !== $this->keyMarker) {
            $query['key-marker'] = $this->keyMarker;
        }
        if (null !== $this->maxUploads) {
            $query['max-uploads'] = (string) $this->maxUploads;
        }
        if (null !== $this->prefix) {
            $query['prefix'] = $this->prefix;
        }
        if (null !== $this->uploadIdMarker) {
            $query['upload-id-marker'] = $this->uploadIdMarker;
        }
        $uri = [];
        if (null === ($v = $this->bucket)) {
            throw new InvalidArgument(\sprintf('Missing parameter "Bucket" for "%s". The value cannot be null.', __CLASS__));
        }
        $uri['Bucket'] = $v;
        $uriString = '/' . \rawurlencode($uri['Bucket']) . '?uploads';
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
     * @param string|null $value
     */
    public function setKeyMarker($value) : self
    {
        $this->keyMarker = $value;
        return $this;
    }
    /**
     * @param int|null $value
     */
    public function setMaxUploads($value) : self
    {
        $this->maxUploads = $value;
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
    public function setUploadIdMarker($value) : self
    {
        $this->uploadIdMarker = $value;
        return $this;
    }
}
