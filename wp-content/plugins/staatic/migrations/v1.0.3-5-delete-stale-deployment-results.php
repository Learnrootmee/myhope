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
            "\n            DELETE rd\n            FROM {$wpdb->prefix}staatic_results_deployment AS rd\n                LEFT JOIN {$wpdb->prefix}staatic_deployments AS d ON d.uuid = rd.deployment_uuid\n            WHERE d.uuid IS NULL\n        "
        );
    }

    /**
     * @param wpdb $wpdb
     * @return void
     */
    public function down($wpdb)
    {
        // Nothing to do here.
    }
};
