<?php

declare(strict_types=1);

namespace Staatic\WordPress\Migrations;

use GlobIterator;
use InvalidArgumentException;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RuntimeException;
use wpdb;

final class Migrator implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var string */
    const DIRECTION_UP = 'up';

    /** @var string */
    const DIRECTION_DOWN = 'down';

    /**
     * @var \wpdb
     */
    private $wpdb;

    /**
     * @var string
     */
    private $migrationsDir;

    public function __construct(wpdb $wpdb, string $migrationsDir)
    {
        $this->wpdb = $wpdb;
        $this->migrationsDir = $migrationsDir;
        $this->logger = new NullLogger();
    }

    /**
     * @param string|null $targetVersion
     * @param string|null $installedVersion
     * @return void
     */
    public function migrate($targetVersion = null, $installedVersion)
    {
        $direction = $this->findDirection($targetVersion, $installedVersion);
        $migrations = $this->findMigrations();
        $migrations = $this->filterMigrations($migrations, $targetVersion, $installedVersion, $direction);
        $migrations = $this->sortMigrations($migrations, $direction);
        // dd(
        //     sprintf("INSTALLED: %s, TARGET: %s, DIRECTION: %s", $installedVersion, $targetVersion, $direction),
        //     $migrations,
        // );
        $this->executeMigrations($migrations, $direction);
    }

    /**
     * @return void
     */
    private function executeMigrations(array $migrations, string $direction)
    {
        foreach ($migrations as $migrationSpec) {
            $this->logger->info("Applying migration '{$migrationSpec['name']}'.");
            if ($direction === self::DIRECTION_UP) {
                $migrationSpec['instance']->up($this->wpdb);
            } else {
                $migrationSpec['instance']->down($this->wpdb);
            }
        }
    }

    /**
     * @param string|null $installedVersion
     */
    private function findDirection(string $targetVersion = null, $installedVersion) : string
    {
        if ($installedVersion === null) {
            return self::DIRECTION_UP;
        }
        if (\version_compare($installedVersion, $targetVersion, '==')) {
            throw new InvalidArgumentException('Installed version and target version cannot be the same!');
        }

        return \version_compare($installedVersion, $targetVersion, '<') ? self::DIRECTION_UP : self::DIRECTION_DOWN;
    }

    /**
     * @param string|null $installedVersion
     */
    private function filterMigrations(
        array $migrations,
        string $targetVersion,
        $installedVersion,
        string $direction
    ) : array
    {
        $filter = $direction === self::DIRECTION_UP ? function ($migrationSpec) use (
            $targetVersion,
            $installedVersion
        ) {
            return ($installedVersion ? \version_compare(
                $migrationSpec['version'],
                $installedVersion,
                '>'
            ) : \true) && \version_compare(
                $migrationSpec['version'],
                $targetVersion,
                '<='
            );
        }
        : function ($migrationSpec) use ($targetVersion, $installedVersion) {
            return \version_compare($migrationSpec['version'], $installedVersion, '<=') && \version_compare(
                $migrationSpec['version'],
                $targetVersion,
                '>'
            );
        };

        return \array_filter($migrations, $filter);
    }

    private function sortMigrations(array $migrations, string $direction) : array
    {
        $comparator = $direction === self::DIRECTION_UP ? function ($a, $b) {
            return $this->compareMigrations($a, $b);
        }
        : function ($a, $b) {
            return $this->compareMigrations($b, $a);
        };
        \uasort($migrations, $comparator);

        return $migrations;
    }

    private function compareMigrations(array $a, array $b) : int
    {
        $versionCompare = \version_compare($a['version'], $b['version']);
        if ($versionCompare !== 0) {
            return $versionCompare;
        }

        return $a['name'] <=> $b['name'];
    }

    private function findMigrations() : array
    {
        if (!\is_dir($this->migrationsDir)) {
            throw new RuntimeException("Migration directory does not exist in {$this->migrationsDir}");
        }
        $pattern = \sprintf('%s/v*.php', \rtrim($this->migrationsDir, '/\\'));
        $iterator = new GlobIterator($pattern);
        $migrations = [];
        foreach ($iterator as $fileInfo) {
            if (\preg_match('~^v(.+?)-(.+?)\\.php$~', $fileInfo->getFilename(), $match) === 0) {
                continue;
            }
            $migration = (require $fileInfo->getPathname());
            if (!$migration instanceof MigrationInterface) {
                continue;
            }
            $migrations[] = [
                'version' => $match[1],
                'name' => $match[2],
                'instance' => $migration
            ];
        }

        return $migrations;
    }
}
