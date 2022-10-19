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
        $this->renameOption('staatic_filesystem_exclude_paths', 'staatic_filesystem_retain_paths');
    }

    /**
     * @param wpdb $wpdb
     * @return void
     */
    public function down($wpdb)
    {
        $this->renameOption('staatic_filesystem_retain_paths', 'staatic_filesystem_exclude_paths');
    }
};
