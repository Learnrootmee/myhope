<?php

namespace Staatic\Framework\DeployStrategy;

use Staatic\Vendor\AsyncAws\Core\Result as AwsResult;
use Staatic\Vendor\AsyncAws\CloudFront\CloudFrontClient;
use Staatic\Vendor\AsyncAws\Core\Exception\Http\HttpException;
use Staatic\Vendor\AsyncAws\S3\Result\PutObjectOutput;
use Staatic\Vendor\AsyncAws\S3\S3Client;
use Staatic\Vendor\GuzzleHttp\Psr7\StreamWrapper;
use InvalidArgumentException;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Symfony\Contracts\HttpClient\HttpClientInterface;
use Staatic\Framework\Deployment;
use Staatic\Framework\Resource;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\Result;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
final class AwsDeployStrategy implements DeployStrategyInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;
    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;
    /**
     * @var S3Client
     */
    private $s3Client;
    /**
     * @var CloudFrontClient
     */
    private $cloudFrontClient;
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var string
     */
    private $basePath = '';
    /**
     * @var string|null
     */
    private $endpoint;
    /**
     * @var string
     */
    private $region;
    /**
     * @var string|null
     */
    private $profile;
    /**
     * @var string|null
     */
    private $accessKeyId;
    /**
     * @var string|null
     */
    private $secretAccessKey;
    /**
     * @var float
     */
    private $timeout = 30;
    /**
     * @var string
     */
    private $bucket;
    /**
     * @var string
     */
    private $prefix;
    /**
     * @var string|null
     */
    private $objectAcl;
    /**
     * @var string|null
     */
    private $distributionId;
    /**
     * @var int
     */
    private $maxInvalidationPaths = 50;
    /**
     * @var string
     */
    private $invalidateEverythingPath = '/*';
    /**
     * @var mixed[]
     */
    private $loggerContext = [];
    public function __construct(ResultRepositoryInterface $resultRepository, ResourceRepositoryInterface $resourceRepository, HttpClientInterface $httpClient, array $options = [])
    {
        $this->logger = new NullLogger();
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
        $this->httpClient = $httpClient;
        if (!empty($options['basePath'])) {
            $this->basePath = \rtrim($options['basePath'], '/');
        }
        if (empty($options['region'])) {
            throw new InvalidArgumentException('Missing required option "region"');
        }
        if (empty($options['bucket'])) {
            throw new InvalidArgumentException('Missing required option "bucket"');
        }
        if (!empty($options['profile']) && !empty($options['accessKeyId']) && !empty($options['secretAccessKey'])) {
            throw new InvalidArgumentException('Option "profile" cannot be used together with option "accessKeyId"');
        }
        $this->endpoint = $options['endpoint'] ?? null;
        $this->region = $options['region'];
        $this->profile = $options['profile'] ?? null;
        $this->accessKeyId = $options['accessKeyId'] ?? null;
        $this->secretAccessKey = $options['secretAccessKey'] ?? null;
        $this->bucket = $options['bucket'];
        $this->prefix = empty($options['prefix']) ? '' : \trim($options['prefix'], '/') . '/';
        $this->objectAcl = empty($options['objectAcl']) ? null : $options['objectAcl'];
        $this->distributionId = $options['distributionId'] ?? null;
        if (isset($options['maxInvalidationPaths']) && $options['maxInvalidationPaths'] !== null) {
            $this->maxInvalidationPaths = $options['maxInvalidationPaths'];
        }
        if (isset($options['invalidateEverythingPath']) && $options['invalidateEverythingPath'] !== null) {
            $this->invalidateEverythingPath = $options['invalidateEverythingPath'];
        }
        $this->s3Client = $this->createS3Client();
        $this->cloudFrontClient = $this->createCloudFrontClient();
    }
    private function createS3Client() : S3Client
    {
        $arguments = ['region' => $this->region];
        if ($this->endpoint) {
            $arguments['endpoint'] = $this->endpoint;
        }
        $arguments = $this->applyCredentials($arguments);
        return new S3Client($arguments, null, $this->httpClient);
    }
    private function createCloudFrontClient() : CloudFrontClient
    {
        $arguments = ['region' => $this->region];
        $arguments = $this->applyCredentials($arguments);
        return new CloudFrontClient($arguments, null, $this->httpClient);
    }
    private function applyCredentials(array $arguments) : array
    {
        if ($this->accessKeyId && $this->secretAccessKey) {
            $arguments['accessKeyId'] = $this->accessKeyId;
            $arguments['accessKeySecret'] = $this->secretAccessKey;
        } elseif ($this->profile) {
            $arguments['profile'] = $this->profile;
        }
        return $arguments;
    }
    /**
     * @param Deployment $deployment
     */
    public function initiate($deployment) : array
    {
        $this->loggerContext = ['deploymentId' => $deployment->id()];
        $localFileHashes = [];
        $localFileResults = [];
        $results = $this->resultRepository->findByBuildIdPendingDeployment($deployment->buildId(), $deployment->id());
        foreach ($results as $result) {
            $key = $this->pathToKey($result->url()->getPath());
            $localFileHashes[$key] = $result->md5();
            $localFileResults[$key] = $result;
        }
        $objects = $this->s3Client->listObjectsV2(['Bucket' => $this->bucket, 'Prefix' => $this->prefix]);
        $remoteFileHashes = [];
        foreach ($objects as $object) {
            $remoteFileHashes[$object->getKey()] = \trim($object->getETag(), '"');
        }
        if ($this->prefix && isset($remoteFileHashes[$this->prefix])) {
            unset($remoteFileHashes[$this->prefix]);
        }
        $diff = $this->diffDeploymentFiles($localFileHashes, $remoteFileHashes);
        foreach ($diff['keep'] as $key => $hash) {
            $result = $localFileResults[$key];
            $this->resultRepository->markDeployed($result, $deployment->id());
        }
        $this->logger->info(\sprintf('Deployment initiated (unmodified files: %d, modified files: "%s", removed files: "%s")', \count($diff['keep']), \implode('", "', \array_keys($diff['upload'])), \implode('", "', \array_keys($diff['delete']))), $this->loggerContext);
        return ['uploadFiles' => \array_keys($diff['upload']), 'deleteFiles' => \array_keys($diff['delete'])];
    }
    /**
     * @param Deployment $deployment
     * @param mixed[] $results
     * @return void
     */
    public function processResults($deployment, $results)
    {
        $this->loggerContext = ['deploymentId' => $deployment->id()];
        $this->logger->info('Deploying results', $this->loggerContext);
        $awsResults = [];
        $pendingResults = [];
        foreach ($results as $result) {
            $resource = $this->resourceRepository->find($result->sha1());
            \assert($resource !== null);
            $awsResults[] = $this->putResultObject($result, $resource);
            $pendingResults[] = $result;
        }
        foreach (AwsResult::wait($awsResults, $this->timeout, \true) as $index => $awsResult) {
            $result = $pendingResults[$index];
            try {
                $awsResult->getETag();
                $this->logger->info("Deployment of result #{$result->id()} was successful", \array_merge($this->loggerContext, ['resultId' => $result->id()]));
            } catch (HttpException $e) {
                $this->logger->error("Deployment of result #{$result->id()} failed: {$e->getMessage()}", \array_merge($this->loggerContext, ['resultId' => $result->id()]));
            }
        }
    }
    private function putResultObject(Result $result, Resource $resource) : PutObjectOutput
    {
        $resource->content()->rewind();
        $arguments = ['Bucket' => $this->bucket, 'Key' => $this->pathToKey($result->url()->getPath()), 'Body' => StreamWrapper::getResource($resource->content()), 'ContentLength' => $resource->size(), 'ContentMD5' => \base64_encode(\hex2bin($resource->md5()))];
        if ($result->mimeType()) {
            $contentType = $result->mimeType();
            if ($result->charset()) {
                $contentType = "{$contentType}; {$result->charset()}";
            }
            $arguments['ContentType'] = $contentType;
        }
        if ($result->redirectUrl() && $this->isSupportedRedirectLocation($result->redirectUrl())) {
            $arguments['WebsiteRedirectLocation'] = (string) $result->redirectUrl();
        }
        if ($this->objectAcl) {
            $arguments['ACL'] = $this->objectAcl;
        }
        return $this->s3Client->putObject($arguments);
    }
    private function isSupportedRedirectLocation(UriInterface $url) : bool
    {
        if (\in_array($url->getScheme(), ['http', 'https'], \true)) {
            return \true;
        }
        if (strncmp($url->getPath(), '/', strlen('/')) === 0) {
            return \true;
        }
        return \false;
    }
    /**
     * @param Deployment $deployment
     * @return void
     */
    public function finish($deployment)
    {
        $this->loggerContext = ['deploymentId' => $deployment->id()];
        $this->logger->info('Finishing deployment', $this->loggerContext);
        $this->deleteStaleFiles($deployment->metadata());
        $this->invalidateCache($deployment->metadata(), $deployment->id());
    }
    /**
     * @return void
     */
    private function deleteStaleFiles(array $deploymentMetadata)
    {
        $awsResults = [];
        $pendingFiles = [];
        foreach ($deploymentMetadata['deleteFiles'] as $key) {
            $awsResults[] = $this->s3Client->deleteObject(['Bucket' => $this->bucket, 'Key' => $key]);
            $pendingFiles[] = $key;
        }
        foreach (AwsResult::wait($awsResults, $this->timeout, \true) as $index => $awsResult) {
            $key = $pendingFiles[$index];
            try {
                $awsResult->getDeleteMarker();
                $this->logger->info("Deletion of stale file {$key} was successful", $this->loggerContext);
            } catch (HttpException $e) {
                $this->logger->error("Deletion of stale file {$key} failed: {$e->getMessage()}", $this->loggerContext);
            }
        }
    }
    /**
     * @return void
     */
    private function invalidateCache(array $deploymentMetadata, string $deploymentId)
    {
        if (!$this->distributionId) {
            return;
        }
        $keys = \array_merge($deploymentMetadata['uploadFiles'], $deploymentMetadata['deleteFiles']);
        $numKeys = \count($keys);
        if ($numKeys === 0) {
            $this->logger->info('No paths to be invalidated in Amazon CloudFront', $this->loggerContext);
            return;
        }
        if ($numKeys > $this->maxInvalidationPaths) {
            $this->logger->notice("Too many paths ({$numKeys}) to be invalidated in Amazon CloudFront, invalidating everything", $this->loggerContext);
            $paths = [$this->invalidateEverythingPath];
        } else {
            $this->logger->info("Invalidating {$numKeys} paths in Amazon CloudFront", $this->loggerContext);
            $paths = \array_map(function ($key) {
                return $this->keyToPath($key);
            }, $keys);
        }
        try {
            $this->cloudFrontClient->createInvalidation(['DistributionId' => $this->distributionId, 'InvalidationBatch' => ['CallerReference' => "staatic/{$deploymentId}", 'Paths' => ['Items' => $paths, 'Quantity' => \count($paths)]]])->resolve($this->timeout);
        } catch (HttpException $e) {
            $this->logger->warning("Unable to invalidate CloudFront cache: {$e->getMessage()}", $this->loggerContext);
        }
    }
    private function diffDeploymentFiles($localFiles, $remoteFiles) : array
    {
        return ['keep' => \array_intersect_assoc($localFiles, $remoteFiles), 'upload' => \array_diff_assoc($localFiles, $remoteFiles), 'delete' => \array_diff_key($remoteFiles, $localFiles)];
    }
    private function pathToKey(string $path) : string
    {
        if ($this->basePath && strncmp($path, $this->basePath, strlen($this->basePath)) === 0) {
            $path = \mb_substr($path, \mb_strlen($this->basePath));
        }
        if (substr_compare($path, '/', -strlen('/')) === 0) {
            $path .= 'index.html';
        }
        return $this->prefix . \ltrim($path, '/');
    }
    private function keyToPath(string $key) : string
    {
        $path = '/' . \substr($key, \strlen($this->prefix));
        if ($path !== '/index.html' && \substr($path, -11) === '/index.html') {
            $path = \substr($path, 0, -10);
        }
        return $path;
    }
}
