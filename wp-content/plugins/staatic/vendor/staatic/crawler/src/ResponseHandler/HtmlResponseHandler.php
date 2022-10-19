<?php

namespace Staatic\Crawler\ResponseHandler;

use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Crawler\CrawlUrl;
use Staatic\Crawler\ResponseUtil;
use Staatic\Crawler\UrlExtractor\HtmlUrlExtractor;
use Staatic\Crawler\UrlExtractor\UrlExtractorInterface;
class HtmlResponseHandler extends AbstractResponseHandler
{
    /**
     * @var UrlExtractorInterface|null
     */
    private $extractor;
    /**
     * @param CrawlUrl $crawlUrl
     */
    public function handle($crawlUrl) : CrawlUrl
    {
        if ($crawlUrl->response() && $this->isHtmlResponse($crawlUrl->response())) {
            return $this->handleHtmlResponse($crawlUrl);
        } else {
            return parent::handle($crawlUrl);
        }
    }
    private function isHtmlResponse(ResponseInterface $response) : bool
    {
        return ResponseUtil::getMimeType($response) === 'text/html';
    }
    private function handleHtmlResponse(CrawlUrl $crawlUrl) : CrawlUrl
    {
        $readMaximumBytes = $this->crawler->crawlOptions()->maxResponseBodyInBytes();
        $responseBody = ResponseUtil::convertBodyToString($crawlUrl->response()->getBody(), $readMaximumBytes);
        $generator = $this->extractor()->extract($responseBody, $crawlUrl->url());
        $this->processExtractedUrls($crawlUrl, $generator);
        $responseBody = Utils::streamFor($generator->getReturn());
        return $crawlUrl->withResponse($crawlUrl->response()->withBody($responseBody));
    }
    private function extractor() : HtmlUrlExtractor
    {
        if (!$this->extractor) {
            $this->extractor = new HtmlUrlExtractor(null, null, $this->crawler->crawlOptions()->htmlUrlExtractorMapping());
            $this->extractor->setFilterCallback($this->urlFilterCallback());
            $this->extractor->setTransformCallback($this->urlTransformCallback());
        }
        return $this->extractor;
    }
}
