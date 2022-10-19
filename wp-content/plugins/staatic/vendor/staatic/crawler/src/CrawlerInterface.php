<?php

namespace Staatic\Crawler;

use Staatic\Vendor\GuzzleHttp\ClientInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use SplSubject;
use Staatic\Crawler\CrawlOptions;
use Staatic\Crawler\CrawlProfile\CrawlProfileInterface;
use Staatic\Crawler\CrawlQueue\CrawlQueueInterface;
use Staatic\Crawler\CrawlUrlProvider\CrawlUrlProviderCollection;
use Staatic\Crawler\Event\EventInterface;
use Staatic\Crawler\KnownUrlsContainer\KnownUrlsContainerInterface;
use Staatic\Crawler\UrlTransformer\UrlTransformation;
interface CrawlerInterface extends SplSubject
{
    const TAG_PRIORITY_HIGH = 'priority_high';
    const TAG_PRIORITY_LOW = 'priority_low';
    const TAG_DONT_TOUCH = 'dont_touch';
    const TAG_DONT_FOLLOW = 'dont_follow';
    const TAG_DONT_SAVE = 'dont_save';
    const TAG_SITEMAP_XML = 'sitemap_xml';
    const TAG_PAGE_NOT_FOUND = 'page_not_found';
    public function __construct(ClientInterface $httpClient, CrawlProfileInterface $crawlProfile, CrawlQueueInterface $crawlQueue, KnownUrlsContainerInterface $knownUrlsContainer, CrawlOptions $crawlOptions);
    /**
     * @param CrawlUrlProviderCollection $crawlUrlProviders
     */
    public function initialize($crawlUrlProviders) : int;
    public function crawl() : int;
    /**
     * @param UriInterface $resolvedUrl
     */
    public function shouldCrawl($resolvedUrl) : bool;
    /**
     * @param CrawlUrl $crawlUrl
     * @return void
     */
    public function addToCrawlQueue($crawlUrl);
    /**
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     */
    public function transformUrl($url, $foundOnUrl = null) : UrlTransformation;
    public function crawlOptions() : CrawlOptions;
    public function numUrlsCrawlable() : int;
    /**
     * @return \Staatic\Crawler\Event\EventInterface|null
     */
    public function getEvent();
    /**
     * @param \Staatic\Crawler\Event\EventInterface $event
     * @return void
     */
    public function setEvent($event);
}
