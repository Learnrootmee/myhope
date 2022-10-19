<?php

namespace Staatic\Crawler\CrawlProfile;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\UrlEvaluator\UrlEvaluatorInterface;
use Staatic\Crawler\UrlNormalizer\UrlNormalizerInterface;
use Staatic\Crawler\UrlTransformer\UrlTransformation;
use Staatic\Crawler\UrlTransformer\UrlTransformerInterface;
abstract class AbstractCrawlProfile implements CrawlProfileInterface
{
    /**
     * @var UriInterface
     */
    protected $baseUrl;
    /**
     * @var UriInterface
     */
    protected $destinationUrl;
    /**
     * @var UrlEvaluatorInterface
     */
    protected $urlEvaluator;
    /**
     * @var UrlNormalizerInterface
     */
    protected $urlNormalizer;
    /**
     * @var UrlTransformerInterface
     */
    protected $urlTransformer;
    public function baseUrl() : UriInterface
    {
        return $this->baseUrl;
    }
    public function destinationUrl() : UriInterface
    {
        return $this->destinationUrl;
    }
    /**
     * @param UriInterface $resolvedUrl
     */
    public function shouldCrawl($resolvedUrl) : bool
    {
        return $this->urlEvaluator->shouldCrawl($resolvedUrl);
    }
    /**
     * @param UriInterface $resolvedUrl
     */
    public function normalizeUrl($resolvedUrl) : UriInterface
    {
        return $this->urlNormalizer->normalize($resolvedUrl);
    }
    /**
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     */
    public function transformUrl($url, $foundOnUrl = null) : UrlTransformation
    {
        return $this->urlTransformer->transform($url, $foundOnUrl);
    }
    /**
     * @return $this
     * @param UrlEvaluatorInterface $urlEvaluator
     */
    public function withUrlEvaluator($urlEvaluator)
    {
        $clone = clone $this;
        $clone->urlEvaluator = $urlEvaluator;
        return $clone;
    }
    /**
     * @return $this
     * @param UrlNormalizerInterface $urlNormalizer
     */
    public function withUrlNormalizer($urlNormalizer)
    {
        $clone = clone $this;
        $clone->urlNormalizer = $urlNormalizer;
        return $clone;
    }
    /**
     * @return $this
     * @param UrlTransformerInterface $urlTransformer
     */
    public function withUrlTransformer($urlTransformer)
    {
        $clone = clone $this;
        $clone->urlTransformer = $urlTransformer;
        return $clone;
    }
}
