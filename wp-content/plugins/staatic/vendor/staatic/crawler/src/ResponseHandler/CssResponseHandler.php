<?php

namespace Staatic\Crawler\ResponseHandler;

use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Crawler\CrawlUrl;
use Staatic\Crawler\ResponseUtil;
use Staatic\Crawler\UrlExtractor\CssUrlExtractor;
use Staatic\Crawler\UrlExtractor\UrlExtractorInterface;
class CssResponseHandler extends AbstractResponseHandler
{
    /**
     * @var UrlExtractorInterface|null
     */
    private $extractor;
    public function __construct()
    {
        $this->extractor = new CssUrlExtractor($this->urlFilterCallback(), $this->urlTransformCallback());
    }
    /**
     * @param CrawlUrl $crawlUrl
     */
    public function handle($crawlUrl) : CrawlUrl
    {
        if ($crawlUrl->response() && $this->isCssResponse($crawlUrl->response())) {
            return $this->handleCssResponse($crawlUrl);
        } else {
            return parent::handle($crawlUrl);
        }
    }
    private function isCssResponse(ResponseInterface $response) : bool
    {
        return ResponseUtil::getMimeType($response) === 'text/css';
    }
    private function handleCssResponse(CrawlUrl $crawlUrl) : CrawlUrl
    {
        $readMaximumBytes = $this->crawler->crawlOptions()->maxResponseBodyInBytes();
        $responseBody = ResponseUtil::convertBodyToString($crawlUrl->response()->getBody(), $readMaximumBytes);
        $generator = $this->extractor->extract($responseBody, $crawlUrl->url());
        $this->processExtractedUrls($crawlUrl, $generator);
        $responseBody = Utils::streamFor($generator->getReturn());
        return $crawlUrl->withResponse($crawlUrl->response()->withBody($responseBody));
    }
}
