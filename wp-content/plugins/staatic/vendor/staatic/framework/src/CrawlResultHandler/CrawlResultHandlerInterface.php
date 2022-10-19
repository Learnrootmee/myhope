<?php

namespace Staatic\Framework\CrawlResultHandler;

use Staatic\Framework\CrawlResult;
interface CrawlResultHandlerInterface
{
    /**
     * @param string $buildId
     * @param CrawlResult $crawlResult
     * @return void
     */
    public function handle($buildId, $crawlResult);
}
