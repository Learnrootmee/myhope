<?php

declare(strict_types=1);

namespace Staatic\Vendor;

use wpdb;
use Staatic\WordPress\Migrations\AbstractMigration;

return new class extends AbstractMigration {
    /**
     * @param wpdb $wpdb
     * @return void
     */
    public function up($wpdb)
    {
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_results DROP resource_id");
    }

    /**
     * @param wpdb $wpdb
     * @return void
     */
    public function down($wpdb)
    {
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_results ADD resource_id VARCHAR(40) NOT NULL DEFAULT '' AFTER status_code"
        );
    }
};
