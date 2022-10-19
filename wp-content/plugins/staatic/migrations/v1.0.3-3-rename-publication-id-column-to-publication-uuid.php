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
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_log_entries CHANGE publication_id publication_uuid binary(16)"
        );
    }

    /**
     * @param wpdb $wpdb
     * @return void
     */
    public function down($wpdb)
    {
        $this->query(
            $wpdb,
            "ALTER TABLE {$wpdb->prefix}staatic_log_entries CHANGE publication_uuid publication_id binary(16)"
        );
    }
};
