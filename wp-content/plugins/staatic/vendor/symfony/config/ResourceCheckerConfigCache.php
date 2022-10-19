<?php

namespace Staatic\Vendor\Symfony\Component\Config;

use Traversable;
use Countable;
use UnexpectedValueException;
use Throwable;
use Staatic\Vendor\Symfony\Component\Config\Resource\ResourceInterface;
use Staatic\Vendor\Symfony\Component\Filesystem\Exception\IOException;
use Staatic\Vendor\Symfony\Component\Filesystem\Filesystem;
class ResourceCheckerConfigCache implements ConfigCacheInterface
{
    /**
     * @var string
     */
    private $file;
    /**
     * @var mixed[]
     */
    private $resourceCheckers;
    /**
     * @param mixed[] $resourceCheckers
     */
    public function __construct(string $file, $resourceCheckers = [])
    {
        $this->file = $file;
        $this->resourceCheckers = $resourceCheckers;
    }
    public function getPath() : string
    {
        return $this->file;
    }
    public function isFresh() : bool
    {
        if (!\is_file($this->file)) {
            return \false;
        }
        if ($this->resourceCheckers instanceof Traversable && !$this->resourceCheckers instanceof Countable) {
            $this->resourceCheckers = \iterator_to_array($this->resourceCheckers);
        }
        if (!\count($this->resourceCheckers)) {
            return \true;
        }
        $metadata = $this->getMetaFile();
        if (!\is_file($metadata)) {
            return \false;
        }
        $meta = $this->safelyUnserialize($metadata);
        if (\false === $meta) {
            return \false;
        }
        $time = \filemtime($this->file);
        foreach ($meta as $resource) {
            foreach ($this->resourceCheckers as $checker) {
                if (!$checker->supports($resource)) {
                    continue;
                }
                if ($checker->isFresh($resource, $time)) {
                    break;
                }
                return \false;
            }
        }
        return \true;
    }
    /**
     * @param string $content
     * @param mixed[]|null $metadata
     */
    public function write($content, $metadata = null)
    {
        $mode = 0666;
        $umask = \umask();
        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->file, $content);
        try {
            $filesystem->chmod($this->file, $mode, $umask);
        } catch (IOException $e) {
        }
        if (null !== $metadata) {
            $filesystem->dumpFile($this->getMetaFile(), \serialize($metadata));
            try {
                $filesystem->chmod($this->getMetaFile(), $mode, $umask);
            } catch (IOException $e) {
            }
        }
        if (\function_exists('opcache_invalidate') && \filter_var(\ini_get('opcache.enable'), \FILTER_VALIDATE_BOOLEAN)) {
            @\opcache_invalidate($this->file, \true);
        }
    }
    private function getMetaFile() : string
    {
        return $this->file . '.meta';
    }
    private function safelyUnserialize(string $file)
    {
        $meta = \false;
        $content = \file_get_contents($file);
        $signalingException = new UnexpectedValueException();
        $prevUnserializeHandler = \ini_set('unserialize_callback_func', self::class . '::handleUnserializeCallback');
        $prevErrorHandler = \set_error_handler(function ($type, $msg, $file, $line, $context = []) use(&$prevErrorHandler, $signalingException) {
            if (__FILE__ === $file) {
                throw $signalingException;
            }
            return $prevErrorHandler ? $prevErrorHandler($type, $msg, $file, $line, $context) : \false;
        });
        try {
            $meta = \unserialize($content);
        } catch (Throwable $e) {
            if ($e !== $signalingException) {
                throw $e;
            }
        } finally {
            \restore_error_handler();
            \ini_set('unserialize_callback_func', $prevUnserializeHandler);
        }
        return $meta;
    }
    /**
     * @param string $class
     */
    public static function handleUnserializeCallback($class)
    {
        \trigger_error('Class not found: ' . $class);
    }
}
