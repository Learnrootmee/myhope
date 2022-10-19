<?php

declare (strict_types=1);
namespace Staatic\Vendor\GuzzleHttp\Psr7;

use InvalidArgumentException;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Vendor\Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
class UploadedFile implements UploadedFileInterface
{
    const ERRORS = [\UPLOAD_ERR_OK, \UPLOAD_ERR_INI_SIZE, \UPLOAD_ERR_FORM_SIZE, \UPLOAD_ERR_PARTIAL, \UPLOAD_ERR_NO_FILE, \UPLOAD_ERR_NO_TMP_DIR, \UPLOAD_ERR_CANT_WRITE, \UPLOAD_ERR_EXTENSION];
    private $clientFilename;
    private $clientMediaType;
    private $error;
    private $file;
    private $moved = \false;
    private $size;
    private $stream;
    /**
     * @param int|null $size
     */
    public function __construct($streamOrFile, $size, int $errorStatus, string $clientFilename = null, string $clientMediaType = null)
    {
        $this->setError($errorStatus);
        $this->size = $size;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
        if ($this->isOk()) {
            $this->setStreamOrFile($streamOrFile);
        }
    }
    /**
     * @return void
     */
    private function setStreamOrFile($streamOrFile)
    {
        if (\is_string($streamOrFile)) {
            $this->file = $streamOrFile;
        } elseif (\is_resource($streamOrFile)) {
            $this->stream = new Stream($streamOrFile);
        } elseif ($streamOrFile instanceof StreamInterface) {
            $this->stream = $streamOrFile;
        } else {
            throw new InvalidArgumentException('Invalid stream or file provided for UploadedFile');
        }
    }
    /**
     * @return void
     */
    private function setError(int $error)
    {
        if (\false === \in_array($error, UploadedFile::ERRORS, \true)) {
            throw new InvalidArgumentException('Invalid error status for UploadedFile');
        }
        $this->error = $error;
    }
    private function isStringNotEmpty($param) : bool
    {
        return \is_string($param) && \false === empty($param);
    }
    private function isOk() : bool
    {
        return $this->error === \UPLOAD_ERR_OK;
    }
    public function isMoved() : bool
    {
        return $this->moved;
    }
    /**
     * @return void
     */
    private function validateActive()
    {
        if (\false === $this->isOk()) {
            throw new RuntimeException('Cannot retrieve stream due to upload error');
        }
        if ($this->isMoved()) {
            throw new RuntimeException('Cannot retrieve stream after it has already been moved');
        }
    }
    public function getStream() : StreamInterface
    {
        $this->validateActive();
        if ($this->stream instanceof StreamInterface) {
            return $this->stream;
        }
        $file = $this->file;
        return new LazyOpenStream($file, 'r+');
    }
    /**
     * @return void
     */
    public function moveTo($targetPath)
    {
        $this->validateActive();
        if (\false === $this->isStringNotEmpty($targetPath)) {
            throw new InvalidArgumentException('Invalid path provided for move operation; must be a non-empty string');
        }
        if ($this->file) {
            $this->moved = \PHP_SAPI === 'cli' ? \rename($this->file, $targetPath) : \move_uploaded_file($this->file, $targetPath);
        } else {
            Utils::copyToStream($this->getStream(), new LazyOpenStream($targetPath, 'w'));
            $this->moved = \true;
        }
        if (\false === $this->moved) {
            throw new RuntimeException(\sprintf('Uploaded file could not be moved to %s', $targetPath));
        }
    }
    /**
     * @return int|null
     */
    public function getSize()
    {
        return $this->size;
    }
    public function getError() : int
    {
        return $this->error;
    }
    /**
     * @return string|null
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }
    /**
     * @return string|null
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }
}
