<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use SplFileInfo;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Staatic\WordPress\Bridge\ResultRepository;
use Staatic\WordPress\Setting\Advanced\WorkDirectorySetting;

final class ResourceCleanup
{
    const CHUNK_SIZE = 50;

    /**
     * @var string
     */
    private $resourceDirectory;

    /**
     * @var ResultRepository
     */
    private $resultRepository;

    /**
     * @var WorkDirectorySetting
     */
    private $workDirectory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        ResultRepository $resultRepository,
        WorkDirectorySetting $workDirectory,
        Filesystem $filesystem
    )
    {
        $this->resultRepository = $resultRepository;
        $this->workDirectory = $workDirectory;
        $this->filesystem = $filesystem;
    }

    /**
     * @return void
     */
    public function cleanup()
    {
        $this->resourceDirectory = \untrailingslashit($this->workDirectory->value()) . '/resources/';
        if (!\is_dir($this->resourceDirectory)) {
            return;
        }
        foreach ($this->obsoletePaths() as $path) {
            $this->filesystem->removeFile($path);
        }
    }

    /** @return iterable<string, string> */
    private function obsoletePaths()
    {
        $flags = FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS;
        $paths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(
            $this->resourceDirectory,
            $flags
        ), RecursiveIteratorIterator::LEAVES_ONLY);
        $chunks = $this->pathsToChunks($paths);
        foreach ($chunks as $chunk) {
            yield from $this->processChunk($chunk);
        }
    }

    /**
     * @param iterable<string, SplFileInfo> $paths
     * @return iterable<int, array>
     **/
    private function pathsToChunks($paths)
    {
        $chunk = [];
        $currentChunkSize = 0;
        foreach ($paths as $path => $fileInfo) {
            if (!$fileInfo->isFile() || !$fileInfo->isWritable()) {
                continue;
            }
            $hash = \strtr($path, [
                $this->resourceDirectory => '',
                '/' => ''
            ]);
            if (\strlen($hash) !== 40) {
                continue;
            }
            $chunk[$path] = $hash;
            $currentChunkSize++;
            if ($currentChunkSize >= self::CHUNK_SIZE) {
                (yield $chunk);
                $chunk = [];
                $currentChunkSize = 0;
            }
        }
        if ($currentChunkSize > 0) {
            (yield $chunk);
        }
    }

    /** @return iterable<string, string> */
    private function processChunk(array $chunk)
    {
        $knownHashes = $this->resultRepository->getKnownSha1Hashes($chunk);
        foreach ($chunk as $path => $hash) {
            if (!\in_array($hash, $knownHashes)) {
                (yield $hash => $path);
            }
        }
    }
}
