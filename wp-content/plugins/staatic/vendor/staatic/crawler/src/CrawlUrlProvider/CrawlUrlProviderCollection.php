<?php

namespace Staatic\Crawler\CrawlUrlProvider;

use ArrayIterator;
use IteratorAggregate;
use Traversable;
final class CrawlUrlProviderCollection implements IteratorAggregate
{
    /**
     * @var mixed[]
     */
    private $providers = [];
    public function __construct(array $providers = [])
    {
        $this->addProviders($providers);
    }
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->providers);
    }
    /**
     * @param mixed[] $providers
     * @return void
     */
    public function addProviders($providers)
    {
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }
    /**
     * @param \Staatic\Crawler\CrawlUrlProvider\CrawlUrlProviderInterface $provider
     * @return void
     */
    public function addProvider($provider)
    {
        $this->providers[] = $provider;
    }
}
