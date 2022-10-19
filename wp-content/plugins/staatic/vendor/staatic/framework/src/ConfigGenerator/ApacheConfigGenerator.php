<?php

namespace Staatic\Framework\ConfigGenerator;

use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\StreamInterface;
use Staatic\Framework\Result;
use Staatic\Framework\Util\PathHelper;
final class ApacheConfigGenerator extends AbstractConfigGenerator
{
    /**
     * @var mixed[]
     */
    private $errorDocuments = [];
    /**
     * @var mixed[]
     */
    private $redirects = [];
    /**
     * @var mixed[]
     */
    private $contentTypeOverrides = [];
    /**
     * @param string|null $notFoundPath
     */
    public function __construct($notFoundPath = null)
    {
        if ($notFoundPath) {
            $this->errorDocuments[404] = $notFoundPath;
        }
    }
    /**
     * @param Result $result
     * @return void
     */
    public function processResult($result)
    {
        if ($result->redirectUrl()) {
            $this->redirects[$result->url()->getPath()] = ['redirectUrl' => $result->redirectUrl(), 'statusCode' => $result->statusCode()];
        } elseif ($this->hasNonStandardMimeType($result) || $this->hasNonUtf8Charset($result)) {
            $this->contentTypeOverrides[$result->url()->getPath()] = ['filePath' => PathHelper::determineFilePath($result->url()->getPath()), 'mimeType' => $result->mimeType(), 'charset' => $result->charset()];
        }
    }
    public function getFiles() : array
    {
        return ['/.htaccess' => $this->generateHtaccessFile()];
    }
    private function generateHtaccessFile() : StreamInterface
    {
        $stream = Utils::streamFor();
        foreach ($this->errorDocuments as $statusCode => $path) {
            $stream->write(\sprintf("ErrorDocument %d %s\n", $statusCode, PathHelper::determineFilePath($path)));
        }
        foreach ($this->redirects as $path => $detail) {
            $stream->write(\sprintf("Redirect %d %s %s\n", $detail['statusCode'], $path, $detail['redirectUrl']));
        }
        foreach ($this->contentTypeOverrides as $path => $detail) {
            $stream->write(\sprintf("\n<If \"%s\">\n  ForceType \"%s\"\n</If>\n", \sprintf('%%{REQUEST_URI} =~ m#^%s#i', \preg_quote($path, '#')), $detail['charset'] ? \sprintf('%s; charset=%s', $detail['mimeType'], $detail['charset']) : $detail['mimeType']));
        }
        $stream->rewind();
        return $stream;
    }
}
