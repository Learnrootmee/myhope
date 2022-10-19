<?php

namespace Staatic\Crawler\CrawlProfile;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\UrlEvaluator\WordPressUrlEvaluator;
use Staatic\Crawler\UrlNormalizer\WordPressUrlNormalizer;
use Staatic\Crawler\UrlTransformer\OfflineUrlTransformer;
use Staatic\Crawler\UrlTransformer\StandardUrlTransformer;
final class WordPressCrawlProfile extends AbstractCrawlProfile
{
    public function __construct(UriInterface $baseUrl, UriInterface $destinationUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->destinationUrl = $destinationUrl;
        $this->urlEvaluator = new WordPressUrlEvaluator($baseUrl);
        $this->urlNormalizer = new WordPressUrlNormalizer();
        if ((string) $destinationUrl === '') {
            $this->urlTransformer = new OfflineUrlTransformer();
        } else {
            $this->urlTransformer = new StandardUrlTransformer($baseUrl, $destinationUrl);
        }
    }
}
