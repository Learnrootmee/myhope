<?php

declare(strict_types=1);

namespace Staatic\WordPress;

use wpdb;

final class Uninstaller
{
    /**
     * @var \wpdb
     */
    private $wpdb;

    public function __construct(wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    /**
     * @return void
     */
    public function uninstall()
    {
        if (\get_option('staatic_uninstall_data')) {
            $this->removeTables();
            $this->removeCapabilities();
        }
        if (\get_option('staatic_uninstall_settings')) {
            $this->removeSettings();
            $this->removeOptions();
        }
    }

    /**
     * @return void
     */
    private function removeTables()
    {
        // var_dump('remove tables'); return; //!
        // Crawler repositories
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->wpdb->prefix}staatic_crawl_queue");
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->wpdb->prefix}staatic_known_urls");
        // Staatic repositories
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->wpdb->prefix}staatic_builds");
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->wpdb->prefix}staatic_results");
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->wpdb->prefix}staatic_deployments");
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->wpdb->prefix}staatic_results_deployment");
        // Plugin repositories
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->wpdb->prefix}staatic_log_entries");
        $this->wpdb->query("DROP TABLE IF EXISTS {$this->wpdb->prefix}staatic_publications");
        \delete_option('staatic_database_version');
    }

    /**
     * @return void
     */
    private function removeCapabilities()
    {
        // var_dump('remove capabilities'); return; //!
        $role = \get_role('administrator');
        $role->remove_cap('staatic_manage_settings');
    }

    /**
     * @return void
     */
    private function removeSettings()
    {
        $settings = [
            'staatic_http_auth_password',
            'staatic_http_auth_username',
            'staatic_http_concurrency',
            'staatic_http_delay',
            'staatic_http_timeout',
            'staatic_http_https_to_http',
            'staatic_logging_level',
            'staatic_page_not_found_path',
            'staatic_ssl_verify_behavior',
            'staatic_ssl_verify_path',
            'staatic_uninstall_data',
            'staatic_uninstall_settings',
            'staatic_work_directory',
            'staatic_additional_paths',
            'staatic_additional_redirects',
            'staatic_additional_urls',
            'staatic_destination_url',
            'staatic_preview_url',
            'staatic_exclude_urls',
            'staatic_deployment_method',
            'staatic_aws_auth_access_key_id',
            'staatic_aws_auth_profile',
            'staatic_aws_auth_secret_access_key',
            'staatic_aws_cloudfront_distribution_id',
            'staatic_aws_cloudfront_invalidate_everything_path',
            'staatic_aws_cloudfront_max_invalidation_paths',
            'staatic_aws_endpoint',
            'staatic_aws_s3_object_acl',
            'staatic_aws_region',
            'staatic_aws_s3_bucket',
            'staatic_aws_s3_prefix',
            'staatic_filesystem_apache_configs',
            'staatic_filesystem_retain_paths',
            'staatic_filesystem_nginx_configs',
            'staatic_filesystem_symlink_uploads',
            'staatic_filesystem_target_directory',
            'staatic_netlify_access_token',
            'staatic_netlify_site_id'
        ];
        foreach ($settings as $setting) {
            // var_dump($setting); continue; //!
            \delete_option($setting);
        }
    }

    /**
     * @return void
     */
    private function removeOptions()
    {
        $options = [
            'staatic_current_publication_id',
            'staatic_latest_publication_id',
            'staatic_active_publication_id',
            'staatic_active_preview_publication_id'
        ];
        foreach ($options as $option) {
            // var_dump($option); continue; //!
            \delete_option($option);
        }
    }
}
