<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use SimpleXMLElement;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
use Staatic\Vendor\AsyncAws\S3\Enum\ServerSideEncryption;
class CompleteMultipartUploadOutput extends Result
{
    private $location;
    private $bucket;
    private $key;
    private $expiration;
    private $etag;
    private $checksumCrc32;
    private $checksumCrc32C;
    private $checksumSha1;
    private $checksumSha256;
    private $serverSideEncryption;
    private $versionId;
    private $sseKmsKeyId;
    private $bucketKeyEnabled;
    private $requestCharged;
    /**
     * @return string|null
     */
    public function getBucket()
    {
        $this->initialize();
        return $this->bucket;
    }
    /**
     * @return bool|null
     */
    public function getBucketKeyEnabled()
    {
        $this->initialize();
        return $this->bucketKeyEnabled;
    }
    /**
     * @return string|null
     */
    public function getChecksumCrc32()
    {
        $this->initialize();
        return $this->checksumCrc32;
    }
    /**
     * @return string|null
     */
    public function getChecksumCrc32C()
    {
        $this->initialize();
        return $this->checksumCrc32C;
    }
    /**
     * @return string|null
     */
    public function getChecksumSha1()
    {
        $this->initialize();
        return $this->checksumSha1;
    }
    /**
     * @return string|null
     */
    public function getChecksumSha256()
    {
        $this->initialize();
        return $this->checksumSha256;
    }
    /**
     * @return string|null
     */
    public function getEtag()
    {
        $this->initialize();
        return $this->etag;
    }
    /**
     * @return string|null
     */
    public function getExpiration()
    {
        $this->initialize();
        return $this->expiration;
    }
    /**
     * @return string|null
     */
    public function getKey()
    {
        $this->initialize();
        return $this->key;
    }
    /**
     * @return string|null
     */
    public function getLocation()
    {
        $this->initialize();
        return $this->location;
    }
    /**
     * @return string|null
     */
    public function getRequestCharged()
    {
        $this->initialize();
        return $this->requestCharged;
    }
    /**
     * @return string|null
     */
    public function getServerSideEncryption()
    {
        $this->initialize();
        return $this->serverSideEncryption;
    }
    /**
     * @return string|null
     */
    public function getSseKmsKeyId()
    {
        $this->initialize();
        return $this->sseKmsKeyId;
    }
    /**
     * @return string|null
     */
    public function getVersionId()
    {
        $this->initialize();
        return $this->versionId;
    }
    /**
     * @param Response $response
     * @return void
     */
    protected function populateResult($response)
    {
        $headers = $response->getHeaders();
        $this->expiration = $headers['x-amz-expiration'][0] ?? null;
        $this->serverSideEncryption = $headers['x-amz-server-side-encryption'][0] ?? null;
        $this->versionId = $headers['x-amz-version-id'][0] ?? null;
        $this->sseKmsKeyId = $headers['x-amz-server-side-encryption-aws-kms-key-id'][0] ?? null;
        $this->bucketKeyEnabled = isset($headers['x-amz-server-side-encryption-bucket-key-enabled'][0]) ? \filter_var($headers['x-amz-server-side-encryption-bucket-key-enabled'][0], \FILTER_VALIDATE_BOOLEAN) : null;
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
        $data = new SimpleXMLElement($response->getContent());
        $this->location = ($v = $data->Location) ? (string) $v : null;
        $this->bucket = ($v = $data->Bucket) ? (string) $v : null;
        $this->key = ($v = $data->Key) ? (string) $v : null;
        $this->etag = ($v = $data->ETag) ? (string) $v : null;
        $this->checksumCrc32 = ($v = $data->ChecksumCRC32) ? (string) $v : null;
        $this->checksumCrc32C = ($v = $data->ChecksumCRC32C) ? (string) $v : null;
        $this->checksumSha1 = ($v = $data->ChecksumSHA1) ? (string) $v : null;
        $this->checksumSha256 = ($v = $data->ChecksumSHA256) ? (string) $v : null;
    }
}
