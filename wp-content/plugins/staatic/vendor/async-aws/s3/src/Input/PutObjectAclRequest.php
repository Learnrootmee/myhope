<?php

namespace Staatic\Vendor\AsyncAws\S3\Input;

use DOMDocument;
use DOMNode;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
use Staatic\Vendor\AsyncAws\Core\Input;
use Staatic\Vendor\AsyncAws\Core\Request;
use Staatic\Vendor\AsyncAws\Core\Stream\StreamFactory;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectCannedACL;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestPayer;
use Staatic\Vendor\AsyncAws\S3\ValueObject\AccessControlPolicy;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Grantee;
use Staatic\Vendor\AsyncAws\S3\ValueObject\Owner;
final class PutObjectAclRequest extends Input
{
    private $acl;
    private $accessControlPolicy;
    private $bucket;
    private $contentMd5;
    private $checksumAlgorithm;
    private $grantFullControl;
    private $grantRead;
    private $grantReadAcp;
    private $grantWrite;
    private $grantWriteAcp;
    private $key;
    private $requestPayer;
    private $versionId;
    private $expectedBucketOwner;
    public function __construct(array $input = [])
    {
        $this->acl = $input['ACL'] ?? null;
        $this->accessControlPolicy = isset($input['AccessControlPolicy']) ? AccessControlPolicy::create($input['AccessControlPolicy']) : null;
        $this->bucket = $input['Bucket'] ?? null;
        $this->contentMd5 = $input['ContentMD5'] ?? null;
        $this->checksumAlgorithm = $input['ChecksumAlgorithm'] ?? null;
        $this->grantFullControl = $input['GrantFullControl'] ?? null;
        $this->grantRead = $input['GrantRead'] ?? null;
        $this->grantReadAcp = $input['GrantReadACP'] ?? null;
        $this->grantWrite = $input['GrantWrite'] ?? null;
        $this->grantWriteAcp = $input['GrantWriteACP'] ?? null;
        $this->key = $input['Key'] ?? null;
        $this->requestPayer = $input['RequestPayer'] ?? null;
        $this->versionId = $input['VersionId'] ?? null;
        $this->expectedBucketOwner = $input['ExpectedBucketOwner'] ?? null;
        parent::__construct($input);
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    /**
     * @return AccessControlPolicy|null
     */
    public function getAccessControlPolicy()
    {
        return $this->accessControlPolicy;
    }
    /**
     * @return string|null
     */
    public function getAcl()
    {
        return $this->acl;
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
    public function getGrantFullControl()
    {
        return $this->grantFullControl;
    }
    /**
     * @return string|null
     */
    public function getGrantRead()
    {
        return $this->grantRead;
    }
    /**
     * @return string|null
     */
    public function getGrantReadAcp()
    {
        return $this->grantReadAcp;
    }
    /**
     * @return string|null
     */
    public function getGrantWrite()
    {
        return $this->grantWrite;
    }
    /**
     * @return string|null
     */
    public function getGrantWriteAcp()
    {
        return $this->grantWriteAcp;
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
    public function getRequestPayer()
    {
        return $this->requestPayer;
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
        if (null !== $this->acl) {
            if (!ObjectCannedACL::exists($this->acl)) {
                throw new InvalidArgument(\sprintf('Invalid parameter "ACL" for "%s". The value "%s" is not a valid "ObjectCannedACL".', __CLASS__, $this->acl));
            }
            $headers['x-amz-acl'] = $this->acl;
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
        if (null !== $this->grantFullControl) {
            $headers['x-amz-grant-full-control'] = $this->grantFullControl;
        }
        if (null !== $this->grantRead) {
            $headers['x-amz-grant-read'] = $this->grantRead;
        }
        if (null !== $this->grantReadAcp) {
            $headers['x-amz-grant-read-acp'] = $this->grantReadAcp;
        }
        if (null !== $this->grantWrite) {
            $headers['x-amz-grant-write'] = $this->grantWrite;
        }
        if (null !== $this->grantWriteAcp) {
            $headers['x-amz-grant-write-acp'] = $this->grantWriteAcp;
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
        if (null !== $this->versionId) {
            $query['versionId'] = $this->versionId;
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
        $uriString = '/' . \rawurlencode($uri['Bucket']) . '/' . \str_replace('%2F', '/', \rawurlencode($uri['Key'])) . '?acl';
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = \false;
        $this->requestBody($document, $document);
        $body = $document->hasChildNodes() ? $document->saveXML() : '';
        return new Request('PUT', $uriString, $query, $headers, StreamFactory::create($body));
    }
    /**
     * @param AccessControlPolicy|null $value
     */
    public function setAccessControlPolicy($value) : self
    {
        $this->accessControlPolicy = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setAcl($value) : self
    {
        $this->acl = $value;
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
    public function setGrantFullControl($value) : self
    {
        $this->grantFullControl = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setGrantRead($value) : self
    {
        $this->grantRead = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setGrantReadAcp($value) : self
    {
        $this->grantReadAcp = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setGrantWrite($value) : self
    {
        $this->grantWrite = $value;
        return $this;
    }
    /**
     * @param string|null $value
     */
    public function setGrantWriteAcp($value) : self
    {
        $this->grantWriteAcp = $value;
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
    public function setVersionId($value) : self
    {
        $this->versionId = $value;
        return $this;
    }
    /**
     * @return void
     */
    private function requestBody(DOMNode $node, DOMDocument $document)
    {
        if (null !== ($v = $this->accessControlPolicy)) {
            $node->appendChild($child = $document->createElement('AccessControlPolicy'));
            $child->setAttribute('xmlns', 'http://s3.amazonaws.com/doc/2006-03-01/');
            $v->requestBody($child, $document);
        }
    }
}
