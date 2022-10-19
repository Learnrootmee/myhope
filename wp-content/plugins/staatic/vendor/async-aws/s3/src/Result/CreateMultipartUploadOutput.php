<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use DateTimeImmutable;
use SimpleXMLElement;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\ChecksumAlgorithm;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
use Staatic\Vendor\AsyncAws\S3\Enum\ServerSideEncryption;
class CreateMultipartUploadOutput extends Result
{
    private $abortDate;
    private $abortRuleId;
    private $bucket;
    private $key;
    private $uploadId;
    private $serverSideEncryption;
    private $sseCustomerAlgorithm;
    private $sseCustomerKeyMd5;
    private $sseKmsKeyId;
    private $sseKmsEncryptionContext;
    private $bucketKeyEnabled;
    private $requestCharged;
    private $checksumAlgorithm;
    /**
     * @return DateTimeImmutable|null
     */
    public function getAbortDate()
    {
        $this->initialize();
        return $this->abortDate;
    }
    /**
     * @return string|null
     */
    public function getAbortRuleId()
    {
        $this->initialize();
        return $this->abortRuleId;
    }
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
    public function getChecksumAlgorithm()
    {
        $this->initialize();
        return $this->checksumAlgorithm;
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
    public function getUploadId()
    {
        $this->initialize();
        return $this->uploadId;
    }
    /**
     * @param Response $response
     * @return void
     */
    protected function populateResult($response)
    {
        $headers = $response->getHeaders();
        $this->abortDate = isset($headers['x-amz-abort-date'][0]) ? new DateTimeImmutable($headers['x-amz-abort-date'][0]) : null;
        $this->abortRuleId = $headers['x-amz-abort-rule-id'][0] ?? null;
        $this->serverSideEncryption = $headers['x-amz-server-side-encryption'][0] ?? null;
        $this->sseCustomerAlgorithm = $headers['x-amz-server-side-encryption-customer-algorithm'][0] ?? null;
        $this->sseCustomerKeyMd5 = $headers['x-amz-server-side-encryption-customer-key-md5'][0] ?? null;
        $this->sseKmsKeyId = $headers['x-amz-server-side-encryption-aws-kms-key-id'][0] ?? null;
        $this->sseKmsEncryptionContext = $headers['x-amz-server-side-encryption-context'][0] ?? null;
        $this->bucketKeyEnabled = isset($headers['x-amz-server-side-encryption-bucket-key-enabled'][0]) ? \filter_var($headers['x-amz-server-side-encryption-bucket-key-enabled'][0], \FILTER_VALIDATE_BOOLEAN) : null;
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
        $this->checksumAlgorithm = $headers['x-amz-checksum-algorithm'][0] ?? null;
        $data = new SimpleXMLElement($response->getContent());
        $this->bucket = ($v = $data->Bucket) ? (string) $v : null;
        $this->key = ($v = $data->Key) ? (string) $v : null;
        $this->uploadId = ($v = $data->UploadId) ? (string) $v : null;
    }
}
