<?php

namespace Staatic\Crawler\CrawlQueue;

use Countable;
use Staatic\Crawler\CrawlUrl;
interface CrawlQueueInterface extends Countable
{
    /**
     * @return void
     */
    public function clear();
    /**
     * @param CrawlUrl $crawlUrl
     * @param int $priority
     * @return void
     */
    public function enqueue($crawlUrl, $priority);
    public function dequeue() : CrawlUrl;
}
