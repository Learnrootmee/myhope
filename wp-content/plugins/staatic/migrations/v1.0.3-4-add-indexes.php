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
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_results ADD INDEX (build_uuid)");
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_log_entries ADD INDEX (publication_uuid)");
    }

    /**
     * @param wpdb $wpdb
     * @return void
     */
    public function down($wpdb)
    {
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_log_entries DROP INDEX publication_uuid");
        $this->query($wpdb, "ALTER TABLE {$wpdb->prefix}staatic_results DROP INDEX build_uuid");
    }
};
