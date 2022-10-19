<?php

declare(strict_types=1);

namespace Staatic\Vendor;

use wpdb;
use Staatic\WordPress\Migrations\AbstractMigration;

return new class extends AbstractMigration {
    private $replacements = [
        'staatic_builds' => ['entry_url', 'destination_url'],
        'staatic_crawl_queue' => ['url', 'origin_url', 'transformed_url', 'found_on_url'],
        'staatic_known_urls' => ['url'],
        'staatic_results' => ['url', 'redirect_url', 'original_url', 'original_found_on_url']
    ];

    /**
     * @param wpdb $wpdb
     * @return void
     */
    public function up($wpdb)
    {
        foreach ($this->replacements as $tableName => $columns) {
            foreach ($columns as $columnName) {
                $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}{$tableName} MODIFY {$columnName} VARCHAR(2083)");
            }
        }
    }

    /**
     * @param wpdb $wpdb
     * @return void
     */
    public function down($wpdb)
    {
        foreach ($this->replacements as $tableName => $columns) {
            foreach ($columns as $columnName) {
                $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}{$tableName} MODIFY {$columnName} VARCHAR(255)");
            }
        }
    }
};
