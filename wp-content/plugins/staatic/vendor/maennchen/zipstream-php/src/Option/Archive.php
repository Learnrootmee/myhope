<?php

declare (strict_types=1);
namespace Staatic\Vendor\ZipStream\Option;

use Staatic\Vendor\Psr\Http\Message\StreamInterface;
final class Archive
{
    const DEFAULT_DEFLATE_LEVEL = 6;
    private $comment = '';
    private $largeFileSize = 20 * 1024 * 1024;
    private $largeFileMethod;
    private $sendHttpHeaders = \false;
    private $httpHeaderCallback = 'header';
    private $enableZip64 = \true;
    private $zeroHeader = \false;
    private $statFiles = \true;
    private $flushOutput = \false;
    private $contentDisposition = 'attachment';
    private $contentType = 'application/x-zip';
    private $deflateLevel = 6;
    private $outputStream;
    public function __construct()
    {
        $this->largeFileMethod = Method::STORE();
        $this->outputStream = \fopen('php://output', 'wb');
    }
    public function getComment() : string
    {
        return $this->comment;
    }
    /**
     * @return void
     */
    public function setComment(string $comment)
    {
        $this->comment = $comment;
    }
    public function getLargeFileSize() : int
    {
        return $this->largeFileSize;
    }
    /**
     * @return void
     */
    public function setLargeFileSize(int $largeFileSize)
    {
        $this->largeFileSize = $largeFileSize;
    }
    public function getLargeFileMethod() : Method
    {
        return $this->largeFileMethod;
    }
    /**
     * @return void
     */
    public function setLargeFileMethod(Method $largeFileMethod)
    {
        $this->largeFileMethod = $largeFileMethod;
    }
    public function isSendHttpHeaders() : bool
    {
        return $this->sendHttpHeaders;
    }
    /**
     * @return void
     */
    public function setSendHttpHeaders(bool $sendHttpHeaders)
    {
        $this->sendHttpHeaders = $sendHttpHeaders;
    }
    public function getHttpHeaderCallback() : callable
    {
        return $this->httpHeaderCallback;
    }
    /**
     * @return void
     */
    public function setHttpHeaderCallback(callable $httpHeaderCallback)
    {
        $this->httpHeaderCallback = $httpHeaderCallback;
    }
    public function isEnableZip64() : bool
    {
        return $this->enableZip64;
    }
    /**
     * @return void
     */
    public function setEnableZip64(bool $enableZip64)
    {
        $this->enableZip64 = $enableZip64;
    }
    public function isZeroHeader() : bool
    {
        return $this->zeroHeader;
    }
    /**
     * @return void
     */
    public function setZeroHeader(bool $zeroHeader)
    {
        $this->zeroHeader = $zeroHeader;
    }
    public function isFlushOutput() : bool
    {
        return $this->flushOutput;
    }
    /**
     * @return void
     */
    public function setFlushOutput(bool $flushOutput)
    {
        $this->flushOutput = $flushOutput;
    }
    public function isStatFiles() : bool
    {
        return $this->statFiles;
    }
    /**
     * @return void
     */
    public function setStatFiles(bool $statFiles)
    {
        $this->statFiles = $statFiles;
    }
    public function getContentDisposition() : string
    {
        return $this->contentDisposition;
    }
    /**
     * @return void
     */
    public function setContentDisposition(string $contentDisposition)
    {
        $this->contentDisposition = $contentDisposition;
    }
    public function getContentType() : string
    {
        return $this->contentType;
    }
    /**
     * @return void
     */
    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;
    }
    public function getOutputStream()
    {
        return $this->outputStream;
    }
    /**
     * @return void
     */
    public function setOutputStream($outputStream)
    {
        $this->outputStream = $outputStream;
    }
    public function getDeflateLevel() : int
    {
        return $this->deflateLevel;
    }
    /**
     * @return void
     */
    public function setDeflateLevel(int $deflateLevel)
    {
        $this->deflateLevel = $deflateLevel;
    }
}
