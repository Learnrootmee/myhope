<?php

declare(strict_types=1);

namespace Staatic\WordPress\Bridge;

use Staatic\Crawler\KnownUrlsContainer\KnownUrlsContainerInterface;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RuntimeException;
use wpdb;

final class KnownUrlsContainer implements KnownUrlsContainerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var \wpdb
     */
    private $wpdb;

    public function __construct(wpdb $wpdb, string $tableName = 'staatic_known_urls')
    {
        $this->wpdb = $wpdb;
        $this->logger = new NullLogger();
        $this->tableName = $wpdb->prefix . $tableName;
    }

    /**
     * @return void
     */
    public function clear()
    {
        $this->logger->debug('Clearing container');
        $result = $this->wpdb->query("DELETE FROM {$this->tableName}");
        if ($result === \false) {
            throw new RuntimeException("Unable to clear container: {$this->wpdb->last_error}");
        }
    }

    /**
     * @param UriInterface $url
     * @return void
     */
    public function add($url)
    {
        $this->logger->debug("Adding url '{$url}' to container");
        $result = $this->wpdb->insert($this->tableName, [
            'hash' => \md5((string) $url),
            'url' => (string) $url
        ]);
        if ($result === \false) {
            throw new RuntimeException("Unable to add url '{$url}' to container: {$this->wpdb->last_error}");
        }
    }

    /**
     * @param UriInterface $url
     */
    public function isKnown($url) : bool
    {
        return (bool) $this->wpdb->get_var(
            $this->wpdb->prepare("SELECT COUNT(*) FROM {$this->tableName} WHERE hash = %s", \md5((string) $url))
        );
    }

    public function count() : int
    {
        return (int) $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->tableName}");
    }
}
