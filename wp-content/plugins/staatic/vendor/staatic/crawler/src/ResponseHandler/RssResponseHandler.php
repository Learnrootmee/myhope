<?php

namespace Staatic\Crawler\ResponseHandler;

use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Crawler\CrawlUrl;
use Staatic\Crawler\ResponseUtil;
use Staatic\Crawler\UrlExtractor\UrlExtractorInterface;
use Staatic\Crawler\UrlExtractor\RssUrlExtractor;
class RssResponseHandler extends AbstractResponseHandler
{
    /**
     * @var UrlExtractorInterface
     */
    private $extractor;
    public function __construct()
    {
        $this->extractor = new RssUrlExtractor($this->urlFilterCallback(), $this->urlTransformCallback());
    }
    /**
     * @param CrawlUrl $crawlUrl
     */
    public function handle($crawlUrl) : CrawlUrl
    {
        if ($this->isRssResponse($crawlUrl->response())) {
            $crawlUrl = $this->handleRssResponse($crawlUrl);
        }
        return parent::handle($crawlUrl);
    }
    private function isRssResponse(ResponseInterface $response) : bool
    {
        return ResponseUtil::getMimeType($response) === 'application/rss+xml';
    }
    private function handleRssResponse(CrawlUrl $crawlUrl) : CrawlUrl
    {
        $readMaximumBytes = $this->crawler->crawlOptions()->maxResponseBodyInBytes();
        $responseBody = ResponseUtil::convertBodyToString($crawlUrl->response()->getBody(), $readMaximumBytes);
        $generator = $this->extractor->extract($responseBody, $crawlUrl->url());
        $this->processExtractedUrls($crawlUrl, $generator);
        $responseBody = Utils::streamFor($generator->getReturn());
        return $crawlUrl->withResponse($crawlUrl->response()->withBody($responseBody));
    }
}
