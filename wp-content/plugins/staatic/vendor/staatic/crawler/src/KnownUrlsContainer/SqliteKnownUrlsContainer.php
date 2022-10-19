<?php

namespace Staatic\Crawler\KnownUrlsContainer;

use Exception;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RuntimeException;
use SQLite3;
final class SqliteKnownUrlsContainer implements KnownUrlsContainerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    const TABLE_DEFINITION = '
        CREATE TABLE IF NOT EXISTS %s (
            url TEXT NOT NULL,
            PRIMARY KEY (url)
        )';
    /**
     * @var \SQLite3
     */
    private $sqlite;
    /**
     * @var string
     */
    private $tableName;
    public function __construct(string $databasePath, string $tableName = 'staatic_known_urls')
    {
        $this->logger = new NullLogger();
        $this->sqlite = new SQLite3($databasePath);
        $this->sqlite->enableExceptions(\true);
        $this->tableName = $tableName;
    }
    public function __destruct()
    {
        $this->sqlite->close();
    }
    /**
     * @return void
     */
    public function createTable()
    {
        try {
            $this->sqlite->exec(\sprintf(self::TABLE_DEFINITION, $this->tableName));
        } catch (Exception $e) {
            throw new RuntimeException("Unable to create known urls table: {$e->getMessage()}");
        }
    }
    /**
     * @return void
     */
    public function clear()
    {
        $this->logger->debug('Clearing container');
        try {
            $this->sqlite->exec("DELETE FROM {$this->tableName}");
        } catch (Exception $e) {
            throw new RuntimeException("Unable to clear container: {$e->getMessage()}");
        }
    }
    /**
     * @param UriInterface $url
     * @return void
     */
    public function add($url)
    {
        $this->logger->debug("Adding url '{$url}' to container");
        try {
            $statement = $this->sqlite->prepare("INSERT INTO {$this->tableName} (url) VALUES (:url)");
            $statement->bindValue(':url', $url, \SQLITE3_TEXT);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to add url '{$url}' to container: {$e->getMessage()}");
        }
    }
    /**
     * @param UriInterface $url
     */
    public function isKnown($url) : bool
    {
        try {
            $statement = $this->sqlite->prepare("SELECT COUNT(*) FROM {$this->tableName} WHERE url = :url");
            $statement->bindValue(':url', $url, \SQLITE3_TEXT);
            $result = $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to count container: {$e->getMessage()}");
        }
        $row = $result->fetchArray(\SQLITE3_NUM);
        return (bool) $row[0];
    }
    public function count() : int
    {
        try {
            $statement = $this->sqlite->prepare("SELECT COUNT(*) FROM {$this->tableName}");
            $result = $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to count container: {$e->getMessage()}");
        }
        $row = $result->fetchArray(\SQLITE3_NUM);
        return (int) $row[0];
    }
}
