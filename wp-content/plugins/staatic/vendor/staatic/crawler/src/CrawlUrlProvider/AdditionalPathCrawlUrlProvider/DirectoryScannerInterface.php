<?php

namespace Staatic\Crawler\CrawlUrlProvider\AdditionalPathCrawlUrlProvider;

use Generator;
interface DirectoryScannerInterface
{
    /**
     * @param mixed[] $excludePaths
     * @return void
     */
    public function setExcludePaths($excludePaths);
    /**
     * @param string $directory
     */
    public function scan($directory) : Generator;
}
