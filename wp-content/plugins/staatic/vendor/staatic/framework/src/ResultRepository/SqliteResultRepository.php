<?php

namespace Staatic\Framework\ResultRepository;

use DateTimeImmutable;
use Exception;
use Generator;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use RuntimeException;
use SQLite3;
use SQLite3Result;
use SQLite3Stmt;
use Staatic\Framework\Result;
final class SqliteResultRepository implements ResultRepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    const TABLE_DEFINITION = '
        CREATE TABLE IF NOT EXISTS %s (
            id TEXT NOT NULL,
            build_id INTEGER NOT NULL,
            url TEXT NOT NULL,
            status_code INTEGER NOT NULL,
            md5 TEXT,
            sha1 TEXT,
            size INTEGER,
            mime_type TEXT,
            charset TEXT,
            redirect_url TEXT,
            original_url TEXT,
            original_found_on_url TEXT,
            date_created TEXT NOT NULL,
            PRIMARY KEY (id)
        )';
    const DEPLOY_TABLE_DEFINITION = '
        CREATE TABLE IF NOT EXISTS %s (
            result_id TEXT NOT NULL,
            deployment_id INTEGER NOT NULL,
            date_created TEXT NOT NULL,
            date_deployed TEXT,
            PRIMARY KEY (result_id, deployment_id)
        )';
    /**
     * @var \SQLite3
     */
    private $sqlite;
    /**
     * @var string
     */
    private $tableName = 'staatic_results';
    /**
     * @var string
     */
    private $deployTableName = 'staatic_results_deployment';
    public function __construct(string $databasePath, string $tableName = 'staatic_results', string $deployTableName = 'staatic_results_deployment')
    {
        $this->tableName = $tableName;
        $this->deployTableName = $deployTableName;
        $this->logger = new NullLogger();
        $this->sqlite = new SQLite3($databasePath);
        $this->sqlite->enableExceptions(\true);
    }
    public function __destruct()
    {
        $this->sqlite->close();
    }
    public function createTables()
    {
        try {
            $this->sqlite->exec(\sprintf(self::TABLE_DEFINITION, $this->tableName));
        } catch (Exception $e) {
            throw new RuntimeException("Unable to create result repository table: {$e->getMessage()}");
        }
        try {
            $this->sqlite->exec(\sprintf(self::DEPLOY_TABLE_DEFINITION, $this->deployTableName));
        } catch (Exception $e) {
            throw new RuntimeException("Unable to create result deploy table: {$e->getMessage()}");
        }
    }
    public function nextId() : string
    {
        return (string) Uuid::uuid4();
    }
    /**
     * @param Result $result
     * @return void
     */
    public function add($result)
    {
        $this->logger->debug("Adding result #{$result->id()}", ['resultId' => $result->id()]);
        try {
            $statement = $this->sqlite->prepare("\n                INSERT INTO {$this->tableName} (\n                    id, build_id, url, status_code, md5, sha1, size, mime_type, charset,\n                    redirect_url, original_url, original_found_on_url, date_created\n                ) VALUES (\n                    :id, :buildId, :url, :statusCode, :md5, :sha1, :size, :mimeType, :charset,\n                    :redirectUrl, :originalUrl, :originalFoundOnUrl, :dateCreated\n                )\n            ");
            $this->bindResultValues($result, $statement);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to add result '{$result->url()}' (#{$result->id()}): {$e->getMessage()}");
        }
    }
    /**
     * @param Result $result
     * @return void
     */
    public function update($result)
    {
        $this->logger->debug("Updating result #{$result->id()}", ['resultId' => $result->id()]);
        try {
            $statement = $this->sqlite->prepare("\n                UPDATE {$this->tableName}\n                SET build_id = :buildId,\n                    url = :url,\n                    status_code = :statusCode,\n                    md5 = :md5,\n                    sha1 = :sha1,\n                    size = :size,\n                    mime_type = :mimeType,\n                    charset = :charset,\n                    redirect_url = :redirectUrl,\n                    original_url = :originalUrl,\n                    original_found_on_url = :originalFoundOnUrl,\n                    date_created = :dateCreated\n                WHERE id = :id\n            ");
            $this->bindResultValues($result, $statement);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to update result #{$result->id()}: {$e->getMessage()}");
        }
    }
    /**
     * @param Result $result
     * @return void
     */
    public function delete($result)
    {
        $this->logger->debug("Deleting result #{$result->id()}", ['resultId' => $result->id()]);
        try {
            $statement = $this->sqlite->prepare("DELETE FROM {$this->tableName} WHERE id = :id");
            $statement->bindValue(':id', $result->id(), \SQLITE3_TEXT);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to delete result #{$result->id()}: {$e->getMessage()}");
        }
    }
    /**
     * @param string $sourceBuildId
     * @param string $targetBuildId
     * @return void
     */
    public function mergeBuildResults($sourceBuildId, $targetBuildId)
    {
        $this->logger->debug("Merging build results from build #{$sourceBuildId} into build #{$targetBuildId}", ['buildId' => $targetBuildId]);
        try {
            $this->doMergeBuildResults($sourceBuildId, $targetBuildId);
        } catch (Exception $e) {
            throw new RuntimeException("Unable to merge build results from build #{$sourceBuildId} into build #{$targetBuildId}: {$e->getMessage()}");
        }
    }
    /**
     * @return void
     */
    private function doMergeBuildResults(string $sourceBuildId, string $targetBuildId)
    {
        $statement = $this->sqlite->prepare("\n            SELECT\n                s.url, s.status_code, s.md5, s.sha1, s.size, s.mime_type, s.charset,\n                s.redirect_url, s.original_url, s.original_found_on_url, s.date_created\n            FROM {$this->tableName} s\n                LEFT JOIN {$this->tableName} t ON t.build_id = :targetBuildId AND t.url = s.url\n            WHERE s.build_id = :sourceBuildId\n                AND t.id IS NULL\n        ");
        $statement->bindValue(':sourceBuildId', $sourceBuildId, \SQLITE3_TEXT);
        $statement->bindValue(':targetBuildId', $targetBuildId, \SQLITE3_TEXT);
        $result = $statement->execute();
        $insertValues = [];
        while ($row = $result->fetchArray(\SQLITE3_ASSOC)) {
            $insertValues[] = ['id' => $this->nextId(), 'build_id' => $targetBuildId] + $row;
            if (\count($insertValues) >= 50) {
                $this->massInsert($this->tableName, $insertValues);
                $insertValues = [];
            }
        }
        if (\count($insertValues)) {
            $this->massInsert($this->tableName, $insertValues);
        }
    }
    /**
     * @return void
     */
    private function massInsert(string $tableName, array $insertValues)
    {
        $columnNames = \array_keys($insertValues[0]);
        $columnCount = \count($insertValues[0]);
        $rowPlaceholders = \array_map(function ($row) use($columnCount) {
            return '(' . \implode(', ', \array_fill(0, $columnCount, '?')) . ')';
        }, $insertValues);
        $statement = $this->sqlite->prepare(\sprintf('
            INSERT INTO %s (' . \implode(', ', $columnNames) . ')
            VALUES ' . \implode(', ', $rowPlaceholders), $tableName));
        $i = 1;
        foreach ($insertValues as $row) {
            foreach ($row as $value) {
                $statement->bindValue($i++, $value, \is_string($value) ? \SQLITE3_TEXT : \SQLITE3_INTEGER);
            }
        }
        $statement->execute();
    }
    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function scheduleForDeployment($buildId, $deploymentId) : int
    {
        $this->logger->debug("Scheduling results in build #{$buildId} for deployment #{$deploymentId}", ['buildId' => $buildId, 'deploymentId' => $deploymentId]);
        try {
            $statement = $this->sqlite->prepare("\n                INSERT INTO {$this->deployTableName} (result_id, deployment_id, date_created)\n                SELECT r.id, :deploymentId, :dateCreated\n                FROM {$this->tableName} r\n                WHERE r.build_id = :buildId\n            ");
            $statement->bindValue(':buildId', $buildId, \SQLITE3_TEXT);
            $statement->bindValue(':deploymentId', $deploymentId, \SQLITE3_TEXT);
            $statement->bindValue(':dateCreated', (new DateTimeImmutable())->format('c'), \SQLITE3_TEXT);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to schedule results in build #{$buildId} for deployment #{$deploymentId}: {$e->getMessage()}");
        }
        return $this->sqlite->changes();
    }
    /**
     * @param Result $result
     * @param string $deploymentId
     * @param bool $force
     */
    public function markDeployable($result, $deploymentId, $force = \false) : bool
    {
        $this->logger->debug("Marking result #{$result->id()} deployable for #{$deploymentId}", ['resultId' => $result->id(), 'deploymentId' => $deploymentId]);
        try {
            $statement = $this->sqlite->prepare("\n                INSERT INTO {$this->deployTableName} (result_id, deployment_id, date_created)\n                VALUES (:resultId, :deploymentId, :dateCreated)\n                ON CONFLICT(result_id, deployment_id) DO " . ($force ? 'UPDATE SET
                    date_deployed = NULL' : 'NOTHING'));
            $statement->bindValue(':resultId', $result->id(), \SQLITE3_TEXT);
            $statement->bindValue(':deploymentId', $deploymentId, \SQLITE3_TEXT);
            $statement->bindValue(':dateCreated', (new DateTimeImmutable())->format('c'), \SQLITE3_TEXT);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to mark result #{$result->id()} deployable for #{$deploymentId}: {$e->getMessage()}");
        }
        return $this->sqlite->changes() === 1;
    }
    /**
     * @param Result $result
     * @param string $deploymentId
     * @return void
     */
    public function markDeployed($result, $deploymentId)
    {
        $this->logger->debug("Marking result #{$result->id()} deployed for deployment #{$deploymentId}", ['resultId' => $result->id(), 'deploymentId' => $deploymentId]);
        try {
            $statement = $this->sqlite->prepare("\n                UPDATE {$this->deployTableName}\n                SET date_deployed = :dateDeployed\n                WHERE result_id = :resultId\n                    AND deployment_id = :deploymentId\n            ");
            $statement->bindValue(':resultId', $result->id(), \SQLITE3_TEXT);
            $statement->bindValue(':deploymentId', $deploymentId, \SQLITE3_TEXT);
            $statement->bindValue(':dateDeployed', (new DateTimeImmutable())->format('c'), \SQLITE3_TEXT);
            $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to mark result #{$result->id()} deployed for deployment #{$deploymentId}: {$e->getMessage()}");
        }
    }
    /**
     * @return void
     */
    private function bindResultValues(Result $result, SQLite3Stmt $statement)
    {
        $statement->bindValue(':id', $result->id(), \SQLITE3_TEXT);
        $statement->bindValue(':buildId', $result->buildId(), \SQLITE3_TEXT);
        $statement->bindValue(':url', $result->url(), \SQLITE3_TEXT);
        $statement->bindValue(':statusCode', $result->statusCode(), \SQLITE3_NUM);
        $statement->bindValue(':md5', $result->md5(), \SQLITE3_TEXT);
        $statement->bindValue(':sha1', $result->sha1(), \SQLITE3_TEXT);
        $statement->bindValue(':size', $result->size(), \SQLITE3_NUM);
        $statement->bindValue(':mimeType', $result->mimeType(), \SQLITE3_TEXT);
        $statement->bindValue(':charset', $result->charset(), \SQLITE3_TEXT);
        $statement->bindValue(':redirectUrl', $result->redirectUrl(), \SQLITE3_TEXT);
        $statement->bindValue(':originalUrl', $result->originalUrl(), \SQLITE3_TEXT);
        $statement->bindValue(':originalFoundOnUrl', $result->originalFoundOnUrl(), \SQLITE3_TEXT);
        $statement->bindValue(':dateCreated', $result->dateCreated()->format('c'), \SQLITE3_TEXT);
    }
    /**
     * @param string $resultId
     * @return Result|null
     */
    public function find($resultId)
    {
        try {
            $statement = $this->sqlite->prepare("SELECT * FROM {$this->tableName} WHERE id = :id");
            $statement->bindValue(':id', $resultId, \SQLITE3_TEXT);
            $result = $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to find result #{$resultId}: {$e->getMessage()}");
        }
        return $this->fetchOneOrNull($result);
    }
    public function findAll() : Generator
    {
        $statement = $this->sqlite->prepare("SELECT * FROM {$this->tableName}");
        $result = $statement->execute();
        while ($row = $result->fetchArray(\SQLITE3_ASSOC)) {
            (yield $this->rowToResult($row));
        }
    }
    /**
     * @param string $buildId
     */
    public function findByBuildId($buildId) : Generator
    {
        $statement = $this->sqlite->prepare("SELECT * FROM {$this->tableName} WHERE build_id = :buildId");
        $statement->bindValue(':buildId', $buildId, \SQLITE3_TEXT);
        $result = $statement->execute();
        while ($row = $result->fetchArray(\SQLITE3_ASSOC)) {
            (yield $this->rowToResult($row));
        }
    }
    /**
     * @param string $buildId
     */
    public function findByBuildIdWithRedirectUrl($buildId) : array
    {
        $statement = $this->sqlite->prepare("SELECT * FROM {$this->tableName} WHERE build_id = :buildId AND redirect_url IS NOT NULL");
        $statement->bindValue(':buildId', $buildId, \SQLITE3_TEXT);
        $result = $statement->execute();
        return $this->fetch($result);
    }
    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function findByBuildIdPendingDeployment($buildId, $deploymentId) : Generator
    {
        $statement = $this->sqlite->prepare("\n            SELECT r.*\n            FROM {$this->tableName} r\n                LEFT JOIN {$this->deployTableName} d ON d.result_id = r.id AND d.deployment_id = :deploymentId\n            WHERE r.build_id = :buildId\n                AND d.date_deployed IS NULL\n        ");
        $statement->bindValue(':buildId', $buildId, \SQLITE3_TEXT);
        $statement->bindValue(':deploymentId', $deploymentId, \SQLITE3_TEXT);
        $result = $statement->execute();
        while ($row = $result->fetchArray(\SQLITE3_ASSOC)) {
            (yield $this->rowToResult($row));
        }
    }
    /**
     * @param string $buildId
     * @param UriInterface $url
     * @return Result|null
     */
    public function findOneByBuildIdAndUrl($buildId, $url)
    {
        try {
            $statement = $this->sqlite->prepare("SELECT * FROM {$this->tableName} WHERE build_id = :buildId AND url = :url");
            $statement->bindValue(':buildId', $buildId, \SQLITE3_TEXT);
            $statement->bindValue(':url', (string) $url, \SQLITE3_TEXT);
            $result = $statement->execute();
        } catch (Exception $e) {
            throw new RuntimeException("Unable to find by build #{$buildId} and url '{$url}': {$e->getMessage()}");
        }
        return $this->fetchOneOrNull($result);
    }
    /**
     * @param string $buildId
     * @param UriInterface $url
     * @return Result|null
     */
    public function findOneByBuildIdAndUrlResolved($buildId, $url)
    {
        $result = $this->findOneByBuildIdAndUrl($buildId, $url);
        if (!$result) {
            return null;
        } elseif ($result->statusCodeCategory() === 3) {
            return $this->findOneByBuildIdAndUrlResolved($buildId, $result->redirectUrl());
        } else {
            return $result;
        }
    }
    /**
     * @param string $buildId
     */
    public function countByBuildId($buildId) : int
    {
        try {
            $statement = $this->sqlite->prepare("SELECT COUNT(*) FROM {$this->tableName} WHERE build_id = :buildId");
            $statement->bindValue(':buildId', $buildId, \SQLITE3_TEXT);
            $result = $statement->execute();
            $row = $result->fetchArray(\SQLITE3_NUM);
        } catch (Exception $e) {
            throw new RuntimeException("Unable to count by build #{$buildId}: {$e->getMessage()}");
        }
        return (int) $row[0];
    }
    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function countByBuildIdPendingDeployment($buildId, $deploymentId) : int
    {
        try {
            $statement = $this->sqlite->prepare("\n                SELECT COUNT(*)\n                FROM {$this->tableName} r\n                    LEFT JOIN {$this->deployTableName} d ON d.result_id = r.id and d.deployment_id = :deploymentId\n                WHERE r.build_id = :buildId\n                    AND d.date_deployed IS NULL\n            ");
            $statement->bindValue(':buildId', $buildId, \SQLITE3_TEXT);
            $statement->bindValue(':deploymentId', $deploymentId, \SQLITE3_TEXT);
            $result = $statement->execute();
            $row = $result->fetchArray(\SQLITE3_NUM);
        } catch (Exception $e) {
            throw new RuntimeException("Unable to count pending deployment by build #{$buildId}: {$e->getMessage()}");
        }
        return (int) $row[0];
    }
    private function fetch(SQLite3Result $result) : array
    {
        $results = [];
        while ($row = $result->fetchArray(\SQLITE3_ASSOC)) {
            $results[] = $this->rowToResult($row);
        }
        return $results;
    }
    /**
     * @return Result|null
     */
    private function fetchOneOrNull(SQLite3Result $result)
    {
        $row = $result->fetchArray(\SQLITE3_ASSOC);
        return \is_array($row) ? $this->rowToResult($row) : null;
    }
    private function rowToResult(array $row) : Result
    {
        return new Result($row['id'], $row['build_id'], new Uri($row['url']), $row['status_code'], $row['md5'], $row['sha1'], $row['size'], $row['mime_type'], $row['charset'], $row['redirect_url'] ? new Uri($row['redirect_url']) : null, $row['original_url'] ? new Uri($row['original_url']) : null, $row['original_found_on_url'] ? new Uri($row['original_found_on_url']) : null, new DateTimeImmutable($row['date_created']));
    }
}
