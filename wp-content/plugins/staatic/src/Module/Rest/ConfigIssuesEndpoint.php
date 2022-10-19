<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Rest;

use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Service\ConfigChecker;

final class ConfigIssuesEndpoint implements ModuleInterface
{
    const NAMESPACE = 'staatic/v1';

    const ENDPOINT = '/config-issues';

    /**
     * @var ConfigChecker
     */
    private $configChecker;

    public function __construct(ConfigChecker $configChecker)
    {
        $this->configChecker = $configChecker;
    }

    /**
     * @return void
     */
    public function hooks()
    {
        \add_action('rest_api_init', [$this, 'registerRoutes']);
    }

    /**
     * @return void
     */
    public function registerRoutes()
    {
        \register_rest_route(self::NAMESPACE, self::ENDPOINT, [[
            'methods' => 'POST',
            'callback' => [$this, 'render'],
            'permission_callback' => [$this, 'permissionCallback'],
            'args' => []
        ]]);
    }

    /**
     * @param \WP_REST_Request $request
     */
    public function render($request)
    {
        $issues = $this->configChecker->findIssues();

        return \rest_ensure_response([
            'issues' => $issues
        ]);
    }

    /**
     * @param \WP_REST_Request $request
     */
    public function permissionCallback($request)
    {
        return \current_user_can('staatic_manage_settings');
    }
}
