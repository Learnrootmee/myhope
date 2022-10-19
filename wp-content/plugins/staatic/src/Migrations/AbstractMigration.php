<?php

declare(strict_types=1);

namespace Staatic\WordPress\Migrations;

use RuntimeException;

abstract class AbstractMigration implements MigrationInterface
{
    /**
     * @param \wpdb $wpdb
     * @param string $query
     */
    protected function query($wpdb, $query)
    {
        $result = $wpdb->query($query);
        if ($result === \false) {
            throw new RuntimeException("Unable to execute query: '{$query}': {$wpdb->last_error}");
        }

        return $result;
    }

    /**
     * @param string $oldName
     * @param string $newName
     * @return void
     */
    protected function renameOption($oldName, $newName)
    {
        $value = \get_option($oldName);
        if ($value === \false) {
            return;
        }
        \update_option($newName, $value);
        \delete_option($oldName);
    }
}
