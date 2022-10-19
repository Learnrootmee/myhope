<?php

namespace Staatic\Framework\ResourceRepository;

use Staatic\Vendor\GuzzleHttp\Psr7\StreamWrapper;
use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use RuntimeException;
use Staatic\Framework\Resource;
use Staatic\Vendor\Symfony\Component\Filesystem\Filesystem;
final class FilesystemResourceRepository implements ResourceRepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var string
     */
    private $targetDirectory;
    public function __construct(string $targetDirectory)
    {
        $this->logger = new NullLogger();
        $this->filesystem = new Filesystem();
        $this->setTargetDirectory($targetDirectory);
    }
    /**
     * @return void
     */
    private function setTargetDirectory(string $targetDirectory)
    {
        if (!\is_dir($targetDirectory)) {
            throw new InvalidArgumentException("Target directory does not exist in {$targetDirectory}");
        }
        $this->targetDirectory = \rtrim($targetDirectory, '/');
    }
    /**
     * @param Resource $resource
     * @return void
     */
    public function write($resource)
    {
        $this->logger->debug("Writing resource with sha1 #{$resource->sha1()}");
        $path = $this->resourcePath($resource->sha1());
        $this->writeResource($resource, $path);
        $this->logger->debug("Wrote resource with sha1 {$resource->sha1()} ({$resource->size()} bytes)");
        $this->relocateResourceStream($resource, $path);
    }
    /**
     * @return void
     */
    private function writeResource(Resource $resource, string $path)
    {
        $this->filesystem->dumpFile($path, StreamWrapper::getResource($resource->content()));
    }
    /**
     * @return void
     */
    private function relocateResourceStream(Resource $resource, string $path)
    {
        $contentStream = Utils::streamFor(\fopen($path, 'r'));
        $resource->replace($contentStream, $resource->md5(), $resource->sha1(), $resource->size());
    }
    /**
     * @param string $sha1
     * @return Resource|null
     */
    public function find($sha1)
    {
        $resourcePath = $this->resourcePath($sha1);
        if (!\is_readable($resourcePath)) {
            return null;
        }
        return Resource::create(\fopen($resourcePath, 'r+'));
    }
    /**
     * @param string $sha1
     * @return void
     */
    public function delete($sha1)
    {
        $resourcePath = $this->resourcePath($sha1);
        if (!\is_readable($resourcePath)) {
            throw new RuntimeException("Unable to find resource with sha1 {$sha1}");
        }
        $this->filesystem->remove($resourcePath);
    }
    private function resourcePath(string $sha1) : string
    {
        return \sprintf('%s/%s/%s', $this->targetDirectory, \substr($sha1, 0, 1), \substr($sha1, 1));
    }
}
