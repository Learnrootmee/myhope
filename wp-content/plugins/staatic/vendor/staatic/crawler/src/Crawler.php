<?php

namespace Staatic\Crawler;

use Staatic\Crawler\Event\StartsCrawling;
use Staatic\Crawler\Event\FinishedCrawling;
use Staatic\Crawler\Event\CrawlRequestFulfilled;
use Staatic\Crawler\Event\CrawlRequestRejected;
use Generator;
use Staatic\Vendor\GuzzleHttp\Exception\RequestException;
use Staatic\Vendor\GuzzleHttp\ClientInterface;
use Staatic\Vendor\GuzzleHttp\Exception\TransferException;
use Staatic\Vendor\GuzzleHttp\Pool;
use Staatic\Vendor\GuzzleHttp\Psr7\Request;
use Staatic\Vendor\GuzzleHttp\RequestOptions;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use SplObjectStorage;
use SplObserver;
use Staatic\Crawler\CrawlOptions;
use Staatic\Crawler\CrawlProfile\CrawlProfileInterface;
use Staatic\Crawler\CrawlQueue\CrawlQueueInterface;
use Staatic\Crawler\CrawlUrlProvider\CrawlUrlProviderCollection;
use Staatic\Crawler\CrawlUrlProvider\CrawlUrlProviderInterface;
use Staatic\Crawler\Event;
use Staatic\Crawler\Event\EventInterface;
use Staatic\Crawler\KnownUrlsContainer\KnownUrlsContainerInterface;
use Staatic\Crawler\UrlTransformer\UrlTransformation;
final class Crawler implements CrawlerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var \SplObjectStorage
     */
    private $observers;
    /**
     * @var \Staatic\Crawler\Event\EventInterface|null
     */
    private $event;
    private $pendingCrawlUrlsById = [];
    /**
     * @var int
     */
    private $numCrawlProcessed = 0;
    /**
     * @var ClientInterface
     */
    private $httpClient;
    /**
     * @var CrawlProfileInterface
     */
    private $crawlProfile;
    /**
     * @var CrawlQueueInterface
     */
    private $crawlQueue;
    /**
     * @var KnownUrlsContainerInterface
     */
    private $knownUrlsContainer;
    /**
     * @var CrawlOptions
     */
    private $crawlOptions;
    public function __construct(ClientInterface $httpClient, CrawlProfileInterface $crawlProfile, CrawlQueueInterface $crawlQueue, KnownUrlsContainerInterface $knownUrlsContainer, CrawlOptions $crawlOptions)
    {
        $this->httpClient = $httpClient;
        $this->crawlProfile = $crawlProfile;
        $this->crawlQueue = $crawlQueue;
        $this->knownUrlsContainer = $knownUrlsContainer;
        $this->crawlOptions = $crawlOptions;
        $this->logger = new NullLogger();
        $this->observers = new SplObjectStorage();
    }
    /**
     * @param CrawlUrlProviderCollection $crawlUrlProviders
     */
    public function initialize($crawlUrlProviders) : int
    {
        $this->crawlQueue->clear();
        $this->knownUrlsContainer->clear();
        $numEnqueued = 0;
        foreach ($crawlUrlProviders as $crawlUrlProvider) {
            $numEnqueued += $this->enqueueProvidedCrawlUrls($crawlUrlProvider);
        }
        return $numEnqueued;
    }
    private function enqueueProvidedCrawlUrls(CrawlUrlProviderInterface $crawlUrlProvider) : int
    {
        $numEnqueued = 0;
        foreach ($crawlUrlProvider->provide() as $crawlUrl) {
            if (!$this->shouldCrawl($crawlUrl->url())) {
                continue;
            }
            if (!$crawlUrl->transformedUrl()) {
                $transformedUrl = $this->transformUrl($crawlUrl->url(), $crawlUrl->foundOnUrl())->transformedUrl();
                $crawlUrl = $crawlUrl->withTransformedUrl($transformedUrl);
            }
            $this->addToCrawlQueue($crawlUrl);
            $numEnqueued++;
        }
        return $numEnqueued;
    }
    public function crawl() : int
    {
        $this->notifyStartsCrawling();
        $this->numCrawlProcessed = 0;
        $this->crawlLoop();
        if ($this->isFinishedCrawling()) {
            $this->notifyFinishedCrawling();
        }
        return $this->numCrawlProcessed;
    }
    /**
     * @return void
     */
    private function crawlLoop()
    {
        while (!$this->isFinishedCrawling() && !$this->maxCrawlsReached()) {
            $this->startCrawlQueue();
        }
    }
    private function maxCrawlsReached() : bool
    {
        $maxCrawls = $this->crawlOptions->maxCrawls();
        return $maxCrawls !== null && $this->numCrawlProcessed >= $maxCrawls;
    }
    private function isFinishedCrawling() : bool
    {
        return \count($this->crawlQueue) === 0;
    }
    private function notifyStartsCrawling()
    {
        $this->setEvent(new StartsCrawling());
        $this->notify();
    }
    private function notifyFinishedCrawling()
    {
        $this->setEvent(new FinishedCrawling());
        $this->notify();
    }
    /**
     * @param UriInterface $resolvedUrl
     */
    public function shouldCrawl($resolvedUrl) : bool
    {
        if (!$this->hasCrawlableScheme($resolvedUrl)) {
            return \false;
        }
        if (!$this->crawlProfile->shouldCrawl($resolvedUrl)) {
            return \false;
        }
        return \true;
    }
    private function hasCrawlableScheme(UriInterface $url) : bool
    {
        return \in_array($url->getScheme(), ['http', 'https']);
    }
    /**
     * @param CrawlUrl $crawlUrl
     * @return void
     */
    public function addToCrawlQueue($crawlUrl)
    {
        if ($this->isKnownUrl($crawlUrl->url())) {
            return;
        }
        $this->addKnownUrl($crawlUrl->url());
        $maxDepth = $this->crawlOptions->maxDepth();
        $forceAssets = $this->crawlOptions->forceAssets();
        if ($forceAssets && $this->determineIsAsset($crawlUrl->url())) {
            $maxDepth = null;
        }
        if ($maxDepth !== null && $crawlUrl->depthLevel() >= $maxDepth) {
            return;
        }
        $priority = $this->determineCrawlUrlPriority($crawlUrl);
        $this->crawlQueue->enqueue($crawlUrl, $priority);
    }
    private function isKnownUrl(UriInterface $resolvedUrl) : bool
    {
        $normalizedUrl = $this->crawlProfile->normalizeUrl($resolvedUrl);
        return $this->knownUrlsContainer->isKnown($normalizedUrl);
    }
    /**
     * @return void
     */
    private function addKnownUrl(UriInterface $resolvedUrl)
    {
        $normalizedUrl = $this->crawlProfile->normalizeUrl($resolvedUrl);
        $this->knownUrlsContainer->add($normalizedUrl);
    }
    private function determineCrawlUrlPriority(CrawlUrl $crawlUrl) : int
    {
        if ($crawlUrl->hasTag(self::TAG_PRIORITY_HIGH)) {
            return 90;
        } elseif ($crawlUrl->hasTag(self::TAG_PRIORITY_LOW)) {
            return 30;
        } else {
            return 60;
        }
    }
    private function determineIsAsset(UriInterface $url) : bool
    {
        return \preg_match($this->crawlOptions->assetsPattern(), $url->getPath()) === 1;
    }
    /**
     * @return void
     */
    private function startCrawlQueue()
    {
        $pool = new Pool($this->httpClient, $this->getHttpRequests(), ['concurrency' => $this->crawlOptions->concurrency(), 'fulfilled' => function (ResponseInterface $response, $index) {
            $this->handleRequestFulfilled($response, $index);
        }, 'rejected' => function (TransferException $transferException, $index) {
            $this->handleRequestRejected($transferException, $index);
        }, 'options' => [RequestOptions::ALLOW_REDIRECTS => \false]]);
        $promise = $pool->promise();
        $promise->wait();
    }
    private function getHttpRequests() : Generator
    {
        while ($this->crawlQueue->count() && !$this->maxCrawlsReached()) {
            $crawlUrl = $this->crawlQueue->dequeue();
            $this->pendingCrawlUrlsById[$crawlUrl->id()] = $crawlUrl;
            $this->numCrawlProcessed++;
            $this->logger->debug("Preparing request for '{$crawlUrl->url()}'", ['crawlUrlId' => $crawlUrl->id()]);
            (yield $crawlUrl->id() => new Request('GET', $crawlUrl->url()));
        }
    }
    /**
     * @return void
     */
    private function handleRequestFulfilled(ResponseInterface $response, string $crawlUrlId)
    {
        $crawlUrl = $this->pendingCrawlUrlsById[$crawlUrlId]->withResponse($response);
        unset($this->pendingCrawlUrlsById[$crawlUrlId]);
        $this->logger->debug("Fulfilled request for '{$crawlUrl->url()}'", ['crawlUrlId' => $crawlUrl->id()]);
        if (!$crawlUrl->hasTag(CrawlerInterface::TAG_DONT_TOUCH)) {
            $responseHandlers = $this->crawlOptions->responseFulfilledHandlers()->toChain($this);
            $crawlUrl = $responseHandlers->handle($crawlUrl);
        }
        $this->notifyCrawlRequestFulfilled($crawlUrl);
    }
    /**
     * @return void
     */
    private function notifyCrawlRequestFulfilled(CrawlUrl $crawlUrl)
    {
        $this->setEvent(CrawlRequestFulfilled::create($crawlUrl));
        $this->notify();
    }
    /**
     * @return void
     */
    private function handleRequestRejected(TransferException $transferException, string $crawlUrlId)
    {
        $crawlUrl = $this->pendingCrawlUrlsById[$crawlUrlId];
        unset($this->pendingCrawlUrlsById[$crawlUrlId]);
        if ($transferException instanceof RequestException) {
            $crawlUrl = $crawlUrl->withResponse($transferException->getResponse());
        }
        $this->logger->debug("Rejected request for '{$crawlUrl->url()}'", ['crawlUrlId' => $crawlUrl->id()]);
        if (!$crawlUrl->hasTag(CrawlerInterface::TAG_DONT_TOUCH)) {
            $responseHandlers = $this->crawlOptions->responseRejectedHandlers()->toChain($this);
            $crawlUrl = $responseHandlers->handle($crawlUrl);
        }
        $this->notifyCrawlRequestRejected($crawlUrl, $transferException);
    }
    /**
     * @return void
     */
    private function notifyCrawlRequestRejected(CrawlUrl $crawlUrl, TransferException $transferException)
    {
        $this->setEvent(CrawlRequestRejected::create($crawlUrl, $transferException));
        $this->notify();
    }
    /**
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     */
    public function transformUrl($url, $foundOnUrl = null) : UrlTransformation
    {
        return $this->crawlProfile->transformUrl($url, $foundOnUrl);
    }
    public function crawlOptions() : CrawlOptions
    {
        return $this->crawlOptions;
    }
    public function numUrlsCrawlable() : int
    {
        return $this->knownUrlsContainer->count();
    }
    /**
     * @return void
     */
    public function attach(SplObserver $observer)
    {
        $this->logger->debug(\sprintf('Attaching observer \'%s\'', \get_class($observer)));
        $this->observers->attach($observer);
    }
    /**
     * @return void
     */
    public function detach(SplObserver $observer)
    {
        $this->logger->debug(\sprintf('Detaching observer \'%s\'', \get_class($observer)));
        $this->observers->detach($observer);
    }
    /**
     * @return void
     */
    public function notify()
    {
        $this->logger->debug(\sprintf('Notifying %d observers about \'%s\'', \count($this->observers), \get_class($this->event)));
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
    /**
     * @return \Staatic\Crawler\Event\EventInterface|null
     */
    public function getEvent()
    {
        return $this->event;
    }
    /**
     * @param \Staatic\Crawler\Event\EventInterface $event
     * @return void
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }
}
