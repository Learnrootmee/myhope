<?php

namespace Staatic\Crawler;

use Staatic\Crawler\ResponseHandler\ResponseHandlerCollection;
use Staatic\Crawler\UrlExtractor\Mapping\HtmlUrlExtractorMapping;
final class CrawlOptions
{
    /**
     * @var int
     */
    private $concurrency = 25;
    /**
     * @var int
     */
    private $maxRedirects = 10;
    /**
     * @var int
     */
    private $maxResponseBodyInBytes = 1024 * 1024 * 16;
    /**
     * @var int|null
     */
    private $maxCrawls;
    /**
     * @var int|null
     */
    private $maxDepth;
    /**
     * @var bool
     */
    private $forceAssets = \false;
    /**
     * @var string
     */
    private $assetsPattern = '/\\.(js|css|png|jpg|gif|eot|woff|woff2|ttf|svg|webp)$/';
    /**
     * @var ResponseHandlerCollection
     */
    private $responseFulfilledHandlers;
    /**
     * @var ResponseHandlerCollection
     */
    private $responseRejectedHandlers;
    /**
     * @var HtmlUrlExtractorMapping
     */
    private $htmlUrlExtractorMapping;
    public function __construct(array $options = [])
    {
        if (isset($options['concurrency'])) {
            $this->setConcurrency($options['concurrency']);
        }
        if (isset($options['maxRedirects'])) {
            $this->setMaxRedirects($options['maxRedirects']);
        }
        if (isset($options['maxResponseBodyInBytes'])) {
            $this->setMaxResponseBodyInBytes($options['maxResponseBodyInBytes']);
        }
        if (isset($options['maxCrawls'])) {
            $this->setMaxCrawls($options['maxCrawls']);
        }
        if (isset($options['maxDepth'])) {
            $this->setMaxDepth($options['maxDepth']);
        }
        if (isset($options['forceAssets'])) {
            $this->setForceAssets($options['forceAssets']);
        }
        if (isset($options['assetsPattern'])) {
            $this->setAssetsPattern($options['assetsPattern']);
        }
        $this->setResponseFulfilledHandlers($options['responseFulfilledHandlers'] ?? ResponseHandlerCollection::createDefaultFulfilledCollection());
        $this->setResponseRejectedHandlers($options['responseRejectedHandlers'] ?? ResponseHandlerCollection::createDefaultRejectedCollection());
        $this->setHtmlUrlExtractorMapping($options['htmlUrlExtractorMapping'] ?? new HtmlUrlExtractorMapping());
    }
    public function setConcurrency(int $concurrency) : self
    {
        $this->concurrency = $concurrency;
        return $this;
    }
    public function concurrency() : int
    {
        return $this->concurrency;
    }
    public function setMaxRedirects(int $maxRedirects) : self
    {
        $this->maxRedirects = $maxRedirects;
        return $this;
    }
    public function maxRedirects() : int
    {
        return $this->maxRedirects;
    }
    /**
     * @param int|null $maxResponseBodyInBytes
     */
    public function setMaxResponseBodyInBytes($maxResponseBodyInBytes) : self
    {
        $this->maxResponseBodyInBytes = $maxResponseBodyInBytes;
        return $this;
    }
    /**
     * @return int|null
     */
    public function maxResponseBodyInBytes()
    {
        return $this->maxResponseBodyInBytes;
    }
    /**
     * @param int|null $maxCrawls
     */
    public function setMaxCrawls($maxCrawls) : self
    {
        $this->maxCrawls = $maxCrawls;
        return $this;
    }
    /**
     * @return int|null
     */
    public function maxCrawls()
    {
        return $this->maxCrawls;
    }
    /**
     * @param int|null $maxDepth
     */
    public function setMaxDepth($maxDepth) : self
    {
        $this->maxDepth = $maxDepth;
        return $this;
    }
    /**
     * @return int|null
     */
    public function maxDepth()
    {
        return $this->maxDepth;
    }
    public function setForceAssets(bool $forceAssets) : self
    {
        $this->forceAssets = $forceAssets;
        return $this;
    }
    public function forceAssets() : bool
    {
        return $this->forceAssets;
    }
    public function setAssetsPattern(string $assetsPattern) : self
    {
        $this->assetsPattern = $assetsPattern;
        return $this;
    }
    public function assetsPattern() : string
    {
        return $this->assetsPattern;
    }
    public function setResponseFulfilledHandlers(ResponseHandlerCollection $handlers) : self
    {
        $this->responseFulfilledHandlers = $handlers;
        return $this;
    }
    public function responseFulfilledHandlers() : ResponseHandlerCollection
    {
        return $this->responseFulfilledHandlers;
    }
    public function setResponseRejectedHandlers(ResponseHandlerCollection $handlers) : self
    {
        $this->responseRejectedHandlers = $handlers;
        return $this;
    }
    public function responseRejectedHandlers() : ResponseHandlerCollection
    {
        return $this->responseRejectedHandlers;
    }
    public function setHtmlUrlExtractorMapping(HtmlUrlExtractorMapping $mapping) : self
    {
        $this->htmlUrlExtractorMapping = $mapping;
        return $this;
    }
    public function htmlUrlExtractorMapping() : HtmlUrlExtractorMapping
    {
        return $this->htmlUrlExtractorMapping;
    }
}
