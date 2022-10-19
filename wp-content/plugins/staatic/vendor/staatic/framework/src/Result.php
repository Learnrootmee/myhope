<?php

namespace Staatic\Framework;

use DateTimeImmutable;
use DateTimeInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class Result
{
    /**
     * @var \DateTimeInterface
     */
    private $dateCreated;
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $buildId;
    /**
     * @var UriInterface
     */
    private $url;
    /**
     * @var int
     */
    private $statusCode;
    /**
     * @var string|null
     */
    private $md5;
    /**
     * @var string|null
     */
    private $sha1;
    /**
     * @var int|null
     */
    private $size;
    /**
     * @var string|null
     */
    private $mimeType;
    /**
     * @var string|null
     */
    private $charset;
    /**
     * @var UriInterface|null
     */
    private $redirectUrl;
    /**
     * @var UriInterface|null
     */
    private $originalUrl;
    /**
     * @var UriInterface|null
     */
    private $originalFoundOnUrl;
    /**
     * @param string|null $md5
     * @param string|null $sha1
     * @param int|null $size
     * @param string|null $mimeType
     * @param string|null $charset
     * @param UriInterface|null $redirectUrl
     * @param UriInterface|null $originalUrl
     * @param UriInterface|null $originalFoundOnUrl
     * @param \DateTimeInterface|null $dateCreated
     */
    public function __construct(string $id, string $buildId, UriInterface $url, int $statusCode, $md5 = null, $sha1 = null, $size = null, $mimeType = null, $charset = null, $redirectUrl = null, $originalUrl = null, $originalFoundOnUrl = null, $dateCreated = null)
    {
        $this->id = $id;
        $this->buildId = $buildId;
        $this->url = $url;
        $this->statusCode = $statusCode;
        $this->md5 = $md5;
        $this->sha1 = $sha1;
        $this->size = $size;
        $this->mimeType = $mimeType;
        $this->charset = $charset;
        $this->redirectUrl = $redirectUrl;
        $this->originalUrl = $originalUrl;
        $this->originalFoundOnUrl = $originalFoundOnUrl;
        $this->dateCreated = $dateCreated ?: new DateTimeImmutable();
    }
    /**
     * @param string $id
     * @param string $buildId
     * @param UriInterface $url
     * @param Resource $resource
     * @param mixed[] $properties
     */
    public static function create($id, $buildId, $url, $resource, $properties = [])
    {
        return new self($id, $buildId, $url, $properties['statusCode'] ?? 200, $resource->md5(), $resource->sha1(), $resource->size(), $properties['mimeType'] ?? 'text/html', $properties['charset'] ?? null, $properties['redirectUrl'] ?? null, $properties['originalUrl'] ?? null, $properties['originalFoundOnUrl'] ?? null, $properties['dateCreated'] ?? null);
    }
    /**
     * @param \Staatic\Framework\Result $originalResult
     * @param string $id
     * @param string $buildId
     */
    public static function createFromResult($originalResult, $id, $buildId) : self
    {
        $result = clone $originalResult;
        $result->id = $id;
        $result->buildId = $buildId;
        return $result;
    }
    public function __toString()
    {
        return \implode(' ~ ', [$this->url, $this->statusCode, $this->mimeType]);
    }
    public function id() : string
    {
        return $this->id;
    }
    public function buildId() : string
    {
        return $this->buildId;
    }
    public function url() : UriInterface
    {
        return $this->url;
    }
    public function statusCode() : int
    {
        return $this->statusCode;
    }
    public function statusCodeCategory() : int
    {
        return (int) \floor($this->statusCode / 100);
    }
    /**
     * @return string|null
     */
    public function md5()
    {
        return $this->md5;
    }
    /**
     * @return string|null
     */
    public function sha1()
    {
        return $this->sha1;
    }
    /**
     * @return int|null
     */
    public function size()
    {
        return $this->size;
    }
    /**
     * @return string|null
     */
    public function mimeType()
    {
        return $this->mimeType;
    }
    /**
     * @return string|null
     */
    public function charset()
    {
        return $this->charset;
    }
    /**
     * @return UriInterface|null
     */
    public function redirectUrl()
    {
        return $this->redirectUrl;
    }
    /**
     * @return UriInterface|null
     */
    public function originalUrl()
    {
        return $this->originalUrl;
    }
    /**
     * @return UriInterface|null
     */
    public function originalFoundOnUrl()
    {
        return $this->originalFoundOnUrl;
    }
    public function dateCreated() : DateTimeInterface
    {
        return $this->dateCreated;
    }
    /**
     * @param Resource $resource
     * @return void
     */
    public function syncResource($resource)
    {
        $this->sha1 = $resource->sha1();
        $this->md5 = $resource->md5();
        $this->size = $resource->size();
    }
}
