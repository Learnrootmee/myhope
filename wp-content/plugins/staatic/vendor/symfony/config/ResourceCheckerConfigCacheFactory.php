<?php

namespace Staatic\Vendor\Symfony\Component\Config;

class ResourceCheckerConfigCacheFactory implements ConfigCacheFactoryInterface
{
    /**
     * @var mixed[]
     */
    private $resourceCheckers = [];
    /**
     * @param mixed[] $resourceCheckers
     */
    public function __construct($resourceCheckers = [])
    {
        $this->resourceCheckers = $resourceCheckers;
    }
    /**
     * @param string $file
     * @param callable $callable
     */
    public function cache($file, $callable) : ConfigCacheInterface
    {
        $cache = new ResourceCheckerConfigCache($file, $this->resourceCheckers);
        if (!$cache->isFresh()) {
            $callable($cache);
        }
        return $cache;
    }
}
