<?php

namespace Staatic\Crawler\UrlExtractor;

interface FilterableInterface
{
    /**
     * @param callable|null $callback
     * @return void
     */
    public function setFilterCallback($callback);
}
