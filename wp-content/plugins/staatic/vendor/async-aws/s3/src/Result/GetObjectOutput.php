<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\Core\Stream\ResultStream;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectLockLegalHoldStatus;
use Staatic\Vendor\AsyncAws\S3\Enum\ObjectLockMode;
use Staatic\Vendor\AsyncAws\S3\Enum\ReplicationStatus;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
use Staatic\Vendor\AsyncAws\S3\Enum\ServerSideEncryption;
use Staatic\Vendor\AsyncAws\S3\Enum\StorageClass;
class GetObjectOutput extends Result
{
    private $body;
    private $deleteMarker;
    private $acceptRanges;
    private $expiration;
    private $restore;
    private $lastModified;
    private $contentLength;
    private $etag;
    private $checksumCrc32;
    private $checksumCrc32C;
    private $checksumSha1;
    private $checksumSha256;
    private $missingMeta;
    private $versionId;
    private $cacheControl;
    private $contentDisposition;
    private $contentEncoding;
    private $contentLanguage;
    private $contentRange;
    private $contentType;
    private $expires;
    private $websiteRedirectLocation;
    private $serverSideEncryption;
    private $metadata;
    private $sseCustomerAlgorithm;
    private $sseCustomerKeyMd5;
    private $sseKmsKeyId;
    private $bucketKeyEnabled;
    private $storageClass;
    private $requestCharged;
    private $replicationStatus;
    private $partsCount;
    private $tagCount;
    private $objectLockMode;
    private $objectLockRetainUntilDate;
    private $objectLockLegalHoldStatus;
    /**
     * @return string|null
     */
    public function getAcceptRanges()
    {
        $this->initialize();
        return $this->acceptRanges;
    }
    public function getBody() : ResultStream
    {
        $this->initialize();
        return $this->body;
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
    public function getCacheControl()
    {
        $this->initialize();
        return $this->cacheControl;
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
    public function getContentDisposition()
    {
        $this->initialize();
        return $this->contentDisposition;
    }
    /**
     * @return string|null
     */
    public function getContentEncoding()
    {
        $this->initialize();
        return $this->contentEncoding;
    }
    /**
     * @return string|null
     */
    public function getContentLanguage()
    {
        $this->initialize();
        return $this->contentLanguage;
    }
    /**
     * @return string|null
     */
    public function getContentLength()
    {
        $this->initialize();
        return $this->contentLength;
    }
    /**
     * @return string|null
     */
    public function getContentRange()
    {
        $this->initialize();
        return $this->contentRange;
    }
    /**
     * @return string|null
     */
    public function getContentType()
    {
        $this->initialize();
        return $this->contentType;
    }
    /**
     * @return bool|null
     */
    public function getDeleteMarker()
    {
        $this->initialize();
        return $this->deleteMarker;
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
     * @return DateTimeImmutable|null
     */
    public function getExpires()
    {
        $this->initialize();
        return $this->expires;
    }
    /**
     * @return DateTimeImmutable|null
     */
    public function getLastModified()
    {
        $this->initialize();
        return $this->lastModified;
    }
    public function getMetadata() : array
    {
        $this->initialize();
        return $this->metadata;
    }
    /**
     * @return int|null
     */
    public function getMissingMeta()
    {
        $this->initialize();
        return $this->missingMeta;
    }
    /**
     * @return string|null
     */
    public function getObjectLockLegalHoldStatus()
    {
        $this->initialize();
        return $this->objectLockLegalHoldStatus;
    }
    /**
     * @return string|null
     */
    public function getObjectLockMode()
    {
        $this->initialize();
        return $this->objectLockMode;
    }
    /**
     * @return DateTimeImmutable|null
     */
    public function getObjectLockRetainUntilDate()
    {
        $this->initialize();
        return $this->objectLockRetainUntilDate;
    }
    /**
     * @return int|null
     */
    public function getPartsCount()
    {
        $this->initialize();
        return $this->partsCount;
    }
    /**
     * @return string|null
     */
    public function getReplicationStatus()
    {
        $this->initialize();
        return $this->replicationStatus;
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
    public function getRestore()
    {
        $this->initialize();
        return $this->restore;
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
    public function getSseKmsKeyId()
    {
        $this->initialize();
        return $this->sseKmsKeyId;
    }
    /**
     * @return string|null
     */
    public function getStorageClass()
    {
        $this->initialize();
        return $this->storageClass;
    }
    /**
     * @return int|null
     */
    public function getTagCount()
    {
        $this->initialize();
        return $this->tagCount;
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
     * @return string|null
     */
    public function getWebsiteRedirectLocation()
    {
        $this->initialize();
        return $this->websiteRedirectLocation;
    }
    /**
     * @param Response $response
     * @return void
     */
    protected function populateResult($response)
    {
        $headers = $response->getHeaders();
        $this->deleteMarker = isset($headers['x-amz-delete-marker'][0]) ? \filter_var($headers['x-amz-delete-marker'][0], \FILTER_VALIDATE_BOOLEAN) : null;
        $this->acceptRanges = $headers['accept-ranges'][0] ?? null;
        $this->expiration = $headers['x-amz-expiration'][0] ?? null;
        $this->restore = $headers['x-amz-restore'][0] ?? null;
        $this->lastModified = isset($headers['last-modified'][0]) ? new DateTimeImmutable($headers['last-modified'][0]) : null;
        $this->contentLength = $headers['content-length'][0] ?? null;
        $this->etag = $headers['etag'][0] ?? null;
        $this->checksumCrc32 = $headers['x-amz-checksum-crc32'][0] ?? null;
        $this->checksumCrc32C = $headers['x-amz-checksum-crc32c'][0] ?? null;
        $this->checksumSha1 = $headers['x-amz-checksum-sha1'][0] ?? null;
        $this->checksumSha256 = $headers['x-amz-checksum-sha256'][0] ?? null;
        $this->missingMeta = isset($headers['x-amz-missing-meta'][0]) ? \filter_var($headers['x-amz-missing-meta'][0], \FILTER_VALIDATE_INT) : null;
        $this->versionId = $headers['x-amz-version-id'][0] ?? null;
        $this->cacheControl = $headers['cache-control'][0] ?? null;
        $this->contentDisposition = $headers['content-disposition'][0] ?? null;
        $this->contentEncoding = $headers['content-encoding'][0] ?? null;
        $this->contentLanguage = $headers['content-language'][0] ?? null;
        $this->contentRange = $headers['content-range'][0] ?? null;
        $this->contentType = $headers['content-type'][0] ?? null;
        $this->expires = isset($headers['expires'][0]) ? new DateTimeImmutable($headers['expires'][0]) : null;
        $this->websiteRedirectLocation = $headers['x-amz-website-redirect-location'][0] ?? null;
        $this->serverSideEncryption = $headers['x-amz-server-side-encryption'][0] ?? null;
        $this->sseCustomerAlgorithm = $headers['x-amz-server-side-encryption-customer-algorithm'][0] ?? null;
        $this->sseCustomerKeyMd5 = $headers['x-amz-server-side-encryption-customer-key-md5'][0] ?? null;
        $this->sseKmsKeyId = $headers['x-amz-server-side-encryption-aws-kms-key-id'][0] ?? null;
        $this->bucketKeyEnabled = isset($headers['x-amz-server-side-encryption-bucket-key-enabled'][0]) ? \filter_var($headers['x-amz-server-side-encryption-bucket-key-enabled'][0], \FILTER_VALIDATE_BOOLEAN) : null;
        $this->storageClass = $headers['x-amz-storage-class'][0] ?? null;
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
        $this->replicationStatus = $headers['x-amz-replication-status'][0] ?? null;
        $this->partsCount = isset($headers['x-amz-mp-parts-count'][0]) ? \filter_var($headers['x-amz-mp-parts-count'][0], \FILTER_VALIDATE_INT) : null;
        $this->tagCount = isset($headers['x-amz-tagging-count'][0]) ? \filter_var($headers['x-amz-tagging-count'][0], \FILTER_VALIDATE_INT) : null;
        $this->objectLockMode = $headers['x-amz-object-lock-mode'][0] ?? null;
        $this->objectLockRetainUntilDate = isset($headers['x-amz-object-lock-retain-until-date'][0]) ? new DateTimeImmutable($headers['x-amz-object-lock-retain-until-date'][0]) : null;
        $this->objectLockLegalHoldStatus = $headers['x-amz-object-lock-legal-hold'][0] ?? null;
        $this->metadata = [];
        foreach ($headers as $name => $value) {
            if ('x-amz-meta-' === \substr($name, 0, 11)) {
                $this->metadata[\substr($name, 11)] = $value[0];
            }
        }
        $this->body = $response->toStream();
    }
}
