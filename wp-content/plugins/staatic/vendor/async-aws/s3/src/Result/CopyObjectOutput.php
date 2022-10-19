<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use SimpleXMLElement;
use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
use Staatic\Vendor\AsyncAws\S3\Enum\ServerSideEncryption;
use Staatic\Vendor\AsyncAws\S3\ValueObject\CopyObjectResult;
class CopyObjectOutput extends Result
{
    private $copyObjectResult;
    private $expiration;
    private $copySourceVersionId;
    private $versionId;
    private $serverSideEncryption;
    private $sseCustomerAlgorithm;
    private $sseCustomerKeyMd5;
    private $sseKmsKeyId;
    private $sseKmsEncryptionContext;
    private $bucketKeyEnabled;
    private $requestCharged;
    /**
     * @return bool|null
     */
    public function getBucketKeyEnabled()
    {
        $this->initialize();
        return $this->bucketKeyEnabled;
    }
    /**
     * @return CopyObjectResult|null
     */
    public function getCopyObjectResult()
    {
        $this->initialize();
        return $this->copyObjectResult;
    }
    /**
     * @return string|null
     */
    public function getCopySourceVersionId()
    {
        $this->initialize();
        return $this->copySourceVersionId;
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
    public function getSseCustomerAlgorithm()
    {
        $this->initialize();
        return $this->sseCustomerAlgorithm;
    }
    /**
     * @return string|null
     */
    public function getSseCustomerKeyMd5()
    {
        $this->initialize();
        return $this->sseCustomerKeyMd5;
    }
    /**
     * @return string|null
     */
    public function getSseKmsEncryptionContext()
    {
        $this->initialize();
        return $this->sseKmsEncryptionContext;
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
        $this->copySourceVersionId = $headers['x-amz-copy-source-version-id'][0] ?? null;
        $this->versionId = $headers['x-amz-version-id'][0] ?? null;
        $this->serverSideEncryption = $headers['x-amz-server-side-encryption'][0] ?? null;
        $this->sseCustomerAlgorithm = $headers['x-amz-server-side-encryption-customer-algorithm'][0] ?? null;
        $this->sseCustomerKeyMd5 = $headers['x-amz-server-side-encryption-customer-key-md5'][0] ?? null;
        $this->sseKmsKeyId = $headers['x-amz-server-side-encryption-aws-kms-key-id'][0] ?? null;
        $this->sseKmsEncryptionContext = $headers['x-amz-server-side-encryption-context'][0] ?? null;
        $this->bucketKeyEnabled = isset($headers['x-amz-server-side-encryption-bucket-key-enabled'][0]) ? \filter_var($headers['x-amz-server-side-encryption-bucket-key-enabled'][0], \FILTER_VALIDATE_BOOLEAN) : null;
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
        $data = new SimpleXMLElement($response->getContent());
        $this->copyObjectResult = new CopyObjectResult(['ETag' => ($v = $data->ETag) ? (string) $v : null, 'LastModified' => ($v = $data->LastModified) ? new DateTimeImmutable((string) $v) : null, 'ChecksumCRC32' => ($v = $data->ChecksumCRC32) ? (string) $v : null, 'ChecksumCRC32C' => ($v = $data->ChecksumCRC32C) ? (string) $v : null, 'ChecksumSHA1' => ($v = $data->ChecksumSHA1) ? (string) $v : null, 'ChecksumSHA256' => ($v = $data->ChecksumSHA256) ? (string) $v : null]);
    }
}
