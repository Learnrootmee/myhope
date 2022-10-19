<?php

namespace Staatic\Crawler\CrawlUrlProvider;

use Generator;
use Staatic\Crawler\CrawlUrlProvider\AdditionalUrlCrawlUrlProvider\AdditionalUrl;
class AdditionalUrlCrawlUrlProvider implements CrawlUrlProviderInterface
{
    /**
     * @var mixed[]
     */
    private $additionalUrls;
    /**
     * @param mixed[] $additionalUrls
     */
    public function __construct($additionalUrls)
    {
        $this->additionalUrls = $additionalUrls;
    }
    public function provide() : Generator
    {
        foreach ($this->additionalUrls as $additionalUrl) {
            (yield $additionalUrl->createCrawlUrl());
        }
    }
}
