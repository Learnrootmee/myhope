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
            "ALTER TABLE {$wpdb->prefix}staatic_results CHANGE resource_uuid resource_id varchar(40) not null"
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
            "ALTER TABLE {$wpdb->prefix}staatic_results CHANGE resource_id resource_uuid varchar(40) not null"
        );
    }
};
