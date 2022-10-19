<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Credentials;

use Staatic\Vendor\AsyncAws\Core\Configuration;
use Staatic\Vendor\Psr\Cache\CacheException;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Symfony\Contracts\Cache\CacheInterface;
use Staatic\Vendor\Symfony\Contracts\Cache\ItemInterface;
final class SymfonyCacheProvider implements CredentialProvider
{
    private $cache;
    private $decorated;
    private $logger;
    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(CredentialProvider $decorated, CacheInterface $cache, $logger = null)
    {
        $this->decorated = $decorated;
        $this->cache = $cache;
        $this->logger = $logger;
    }
    /**
     * @param Configuration $configuration
     * @return Credentials|null
     */
    public function getCredentials($configuration)
    {
        $provider = $this->decorated;
        $callable = static function (ItemInterface $item) use($configuration, $provider) {
            $credential = $provider->getCredentials($configuration);
            if (null !== $credential && null !== ($exp = $credential->getExpireDate())) {
                $item->expiresAt($exp);
            } else {
                $item->expiresAfter(0);
            }
            return $credential;
        };
        $closure = function () use ($callable) {
            return $callable(...func_get_args());
        };
        try {
            return $this->cache->get('AsyncAws.Credentials.' . \sha1(\serialize([$configuration, \get_class($this->decorated)])), $closure);
        } catch (CacheException $e) {
            if (null !== $this->logger) {
                $this->logger->error('Failed to get AWS credentials from cache.', ['exception' => $e]);
            }
            return $provider->getCredentials($configuration);
        }
    }
}
