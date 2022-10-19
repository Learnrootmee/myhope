<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use wpdb;

final class Diagnostics
{
    /**
     * @var \wpdb
     */
    private $wpdb;

    public function __construct(wpdb $wpdb)
    {
        $this->wpdb = $wpdb;
    }

    public function retrieve() : array
    {
        $environment = [
            'general' => $this->retrieveGeneral(),
            'options' => $this->retrieveOptions(),
            'plugins' => $this->retrievePlugins(),
            'theme' => $this->retrieveTheme()
        ];

        return $environment;
    }

    private function retrieveGeneral() : array
    {
        global $wp_version;
        $general = [
            'staatic_version' => \STAATIC_VERSION,
            'php_version' => \phpversion(),
            'php_extensions' => \get_loaded_extensions(),
            'php_max_execution_time' => \ini_get('max_execution_time'),
            'php_upload_max_filesize' => \ini_get('upload_max_filesize'),
            'wordpress_version' => $wp_version,
            'is_multisite' => \is_multisite(),
            'https' => \wp_is_using_https()
        ];

        return $general;
    }

    private function retrieveOptions() : array
    {
        $options = \array_flip([
            'siteurl', 'home', 'permalink_structure', 'blog_charset', 'staatic_destination_url', 'staatic_deployment_method', 'staatic_http_auth_username', 'staatic_http_concurrency', 'staatic_http_delay', 'staatic_http_https_to_http', 'staatic_http_timeout', 'staatic_ssl_verify_behavior', 'staatic_ssl_verify_path']
        );
        foreach ($options as $option => &$value) {
            $value = \get_option($option, null);
        }

        return $options;
    }

    private function retrievePlugins() : array
    {
        if (!\function_exists('get_plugins')) {
            require_once \ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugins = \get_plugins();

        return \array_map(function (array $plugin, string $pluginFile) {
            return [
                'name' => $plugin['Name'],
                'uri' => $plugin['PluginURI'],
                'version' => $plugin['Version'],
                'author' => $plugin['Author'],
                'author_uri' => $plugin['AuthorURI'],
                'status' => $this->getPluginStatus($pluginFile)
            ];
        }, $plugins, \array_keys($plugins));
    }

    private function retrieveTheme() : array
    {
        $theme = \wp_get_theme();

        return [
            'name' => $theme->get('Name'),
            'uri' => $theme->get('ThemeURI'),
            'version' => $theme->get('Version'),
            'author' => $theme->get('Author'),
            'author_uri' => $theme->get('AuthorURI')
        ];
    }

    private function getPluginStatus(string $pluginFile) : string
    {
        if (\is_plugin_active_for_network($pluginFile)) {
            return 'active-network';
        }
        if (\is_plugin_active($pluginFile)) {
            return 'active';
        }

        return 'inactive';
    }
}
