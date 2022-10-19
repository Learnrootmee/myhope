<?php

namespace Staatic\Framework;

use Staatic\Vendor\GuzzleHttp\Exception\TransferException;
use Staatic\Vendor\Psr\Http\Message\ResponseInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\NullLogger;
use SplObserver;
use Staatic\Crawler\CrawlerInterface;
use Staatic\Crawler\CrawlUrlProvider\CrawlUrlProviderCollection;
use Staatic\Crawler\Observer\CallbackObserver;
use Staatic\Framework\BuildRepository\BuildRepositoryInterface;
use Staatic\Framework\CrawlResultHandler\CrawlResultHandler;
use Staatic\Framework\CrawlResultHandler\CrawlResultHandlerInterface;
use Staatic\Framework\PostProcessor\PostProcessorCollection;
use Staatic\Framework\ResourceRepository\ResourceRepositoryInterface;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\Framework\Transformer\TransformerCollection;
class StaticGenerator implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var TransformerCollection
     */
    private $transformers;
    /**
     * @var PostProcessorCollection
     */
    private $postProcessors;
    /**
     * @var CrawlerInterface
     */
    private $crawler;
    /**
     * @var BuildRepositoryInterface
     */
    private $buildRepository;
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;
    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;
    /**
     * @param TransformerCollection|null $transformers
     * @param PostProcessorCollection|null $postProcessors
     * @param LoggerInterface|null $logger
     */
    public function __construct(CrawlerInterface $crawler, BuildRepositoryInterface $buildRepository, ResultRepositoryInterface $resultRepository, ResourceRepositoryInterface $resourceRepository, $transformers = null, $postProcessors = null, $logger = null)
    {
        $this->crawler = $crawler;
        $this->buildRepository = $buildRepository;
        $this->resultRepository = $resultRepository;
        $this->resourceRepository = $resourceRepository;
        $this->transformers = $transformers ?: new TransformerCollection();
        $this->postProcessors = $postProcessors ?: new PostProcessorCollection();
        $this->logger = $logger ?: new NullLogger();
        $this->crawlResultHandler = $this->createCrawlResultHandler();
    }
    /**
     * @param Build $build
     * @param CrawlUrlProviderCollection $crawlUrlProviders
     */
    public function initializeCrawler($build, $crawlUrlProviders) : int
    {
        $this->logger->notice('Initializing crawler', ['buildId' => $build->id()]);
        $numEnqueued = $this->crawler->initialize($crawlUrlProviders);
        $this->updateInitializeCrawlerStats($build, $numEnqueued);
        $this->logger->notice("Finished initializing crawler ({$numEnqueued} enqueued)", ['buildId' => $build->id()]);
        return $numEnqueued;
    }
    /**
     * @return void
     */
    private function updateInitializeCrawlerStats(Build $build, int $numCrawled)
    {
        $build->queuedUrls($numCrawled);
        $this->buildRepository->update($build);
    }
    /**
     * @param Build $build
     */
    public function crawl($build) : bool
    {
        $observer = $this->createCrawlerObserver($build);
        $this->crawler->attach($observer);
        $numCrawled = $this->crawler->crawl();
        $this->crawler->detach($observer);
        $this->updateCrawlStats($build, $numCrawled);
        return $build->isFinishedCrawling();
    }
    /**
     * @return void
     */
    private function updateCrawlStats(Build $build, int $numCrawled)
    {
        $numUrlsCrawlable = $this->crawler->numUrlsCrawlable();
        $build->crawledUrls($numUrlsCrawlable, $numCrawled);
        $this->buildRepository->update($build);
    }
    /**
     * @param Build $build
     * @return void
     */
    public function finish($build)
    {
        $this->logger->notice('Finishing build', ['buildId' => $build->id()]);
        if ($build->parentId()) {
            $this->logger->info("Merging build results", ['buildId' => $build->id()]);
            $this->resultRepository->mergeBuildResults($build->parentId(), $build->id());
        }
        $this->logger->notice('Finished build', ['buildId' => $build->id()]);
    }
    private function createCrawlResultHandler() : CrawlResultHandlerInterface
    {
        return new CrawlResultHandler($this->resultRepository, $this->resourceRepository, $this->transformers);
    }
    private function createCrawlerObserver(Build $build) : SplObserver
    {
        return new CallbackObserver(function (UriInterface $url, UriInterface $transformedUrl, ResponseInterface $response, $foundOnUrl, array $tags) use($build) {
            $this->logger->log(\in_array(CrawlerInterface::TAG_PAGE_NOT_FOUND, $tags) ? 'warning' : 'info', "Crawl '{$url}' fulfilled", ['buildId' => $build->id()]);
            if (\in_array(CrawlerInterface::TAG_DONT_SAVE, $tags)) {
                return;
            }
            $this->handleCrawlResult($build, CrawlResult::fromFulfilledCrawlRequest($url, $transformedUrl, $response, $foundOnUrl));
        }, function (UriInterface $url, UriInterface $transformedUrl, TransferException $transferException, $foundOnUrl, array $tags) use($build) {
            $this->logger->log(\in_array(CrawlerInterface::TAG_PAGE_NOT_FOUND, $tags) ? 'info' : 'warning', "Crawl '{$url}' rejected ({$transferException->getMessage()}) (found on {$foundOnUrl})", ['buildId' => $build->id()]);
            if (\in_array(CrawlerInterface::TAG_DONT_SAVE, $tags)) {
                return;
            }
            $this->handleCrawlResult($build, CrawlResult::fromRejectedCrawlRequest($url, $transformedUrl, $transferException, $foundOnUrl));
        }, function () use($build) {
            if ($build->dateCrawlStarted()) {
                return;
            }
            $this->logger->notice("Crawling started for '{$build->entryUrl()}'", ['buildId' => $build->id()]);
            $build->crawlStarted();
            $this->buildRepository->update($build);
        }, function () use($build) {
            $this->logger->notice("Crawling finished for '{$build->entryUrl()}'", ['buildId' => $build->id()]);
            $build->crawlFinished();
            $this->buildRepository->update($build);
        });
    }
    /**
     * @return void
     */
    private function handleCrawlResult(Build $build, CrawlResult $crawlResult)
    {
        $this->crawlResultHandler->handle($build->id(), $crawlResult);
    }
    /**
     * @param Build $build
     * @return void
     */
    public function postProcess($build)
    {
        $this->logger->notice('Starting post processing', ['buildId' => $build->id()]);
        $this->postProcessors->apply($build);
        $this->logger->notice('Finished post processing', ['buildId' => $build->id()]);
    }
}
