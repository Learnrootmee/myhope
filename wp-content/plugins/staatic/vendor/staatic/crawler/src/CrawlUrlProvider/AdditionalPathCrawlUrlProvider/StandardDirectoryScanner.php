<?php

namespace Staatic\Crawler\CrawlUrlProvider\AdditionalPathCrawlUrlProvider;

use Generator;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
final class StandardDirectoryScanner implements DirectoryScannerInterface
{
    /**
     * @var bool
     */
    private $excludeHiddenFiles;
    /**
     * @var mixed[]
     */
    private $excludePaths;
    public function __construct(array $excludePaths = [], bool $excludeHiddenFiles = \true)
    {
        $this->setExcludePaths($excludePaths);
        $this->setExcludeHiddenFiles($excludeHiddenFiles);
    }
    public function excludePaths() : array
    {
        return $this->excludePaths;
    }
    /**
     * @param mixed[] $excludePaths
     * @return void
     */
    public function setExcludePaths($excludePaths)
    {
        $this->excludePaths = \array_map(function ($path) {
            return $this->normalizePath($path);
        }, $excludePaths);
    }
    private function normalizePath(string $path) : string
    {
        if (\DIRECTORY_SEPARATOR === '\\') {
            $path = \str_replace('\\', '/', $path);
        }
        if (\substr($path, 1, 1) === ':') {
            $path = \ucfirst($path);
        }
        return \rtrim($path, '/\\');
    }
    public function excludeHiddenFiles() : bool
    {
        return $this->excludeHiddenFiles;
    }
    /**
     * @param bool $excludeHiddenFiles
     * @return void
     */
    public function setExcludeHiddenFiles($excludeHiddenFiles)
    {
        $this->excludeHiddenFiles = $excludeHiddenFiles;
    }
    /**
     * @param string $directory
     */
    public function scan($directory) : Generator
    {
        $flags = RecursiveDirectoryIterator::SKIP_DOTS | RecursiveDirectoryIterator::FOLLOW_SYMLINKS;
        yield from new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(new RecursiveDirectoryIterator($directory, $flags), function ($fileInfo, $path, $iterator) {
            return !$this->shouldExcludePath($path);
        }));
    }
    private function shouldExcludePath(string $path) : bool
    {
        if ($this->excludeHiddenFiles && strncmp(\basename($path), '.', strlen('.')) === 0) {
            return \true;
        }
        $path = $this->normalizePath($path);
        return \in_array($path, $this->excludePaths);
    }
}
