<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication\Task;

use Staatic\WordPress\Factory\StaticGeneratorFactory;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Setting\Advanced\HttpConcurrencySetting;

final class CrawlTask implements TaskInterface
{
    /** @var int */
    const CRAWL_MAX_BATCH_SIZE = 12;

    /**
     * @var HttpConcurrencySetting
     */
    private $httpConcurrency;

    /**
     * @var StaticGeneratorFactory
     */
    private $factory;

    public function __construct(HttpConcurrencySetting $httpConcurrency, StaticGeneratorFactory $factory)
    {
        $this->httpConcurrency = $httpConcurrency;
        $this->factory = $factory;
    }

    public function name() : string
    {
        return 'crawl';
    }

    public function description() : string
    {
        return \__('Crawling WordPress site', 'staatic');
    }

    /**
     * @param Publication $publication
     */
    public function supports($publication) : bool
    {
        if ($publication->metadataByKey('sourcePublicationId')) {
            // Skip in case an existing publication was this publication's source.
            return \false;
        }

        return \true;
    }

    /**
     * @param Publication $publication
     */
    public function execute($publication) : bool
    {
        $batchSize = \min(self::CRAWL_MAX_BATCH_SIZE, $this->httpConcurrency->value() * 2);
        // $batchSize = 1; //!TODO: remove me!!!
        $staticGenerator = ($this->factory)($publication, $batchSize);
        $crawlFinished = $staticGenerator->crawl($publication->build());

        return $crawlFinished;
    }
}
