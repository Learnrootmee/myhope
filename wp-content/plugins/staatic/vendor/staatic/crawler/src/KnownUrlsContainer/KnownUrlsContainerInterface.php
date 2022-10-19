<?php

namespace Staatic\Crawler\KnownUrlsContainer;

use Countable;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
interface KnownUrlsContainerInterface extends Countable
{
    /**
     * @return void
     */
    public function clear();
    /**
     * @param UriInterface $url
     * @return void
     */
    public function add($url);
    /**
     * @param UriInterface $url
     */
    public function isKnown($url) : bool;
}
