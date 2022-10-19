<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\ZipfileDeployer;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\Task\DeployTask;
use Staatic\WordPress\Publication\Task\FinishDeploymentTask;
use Staatic\WordPress\Publication\Task\InitiateDeploymentTask;
use Staatic\WordPress\Service\Settings;

final class ZipfileDeployerModule implements ModuleInterface
{
    const DEPLOYMENT_METHOD_NAME = 'zipfile';

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var ServiceLocator
     */
    private $settingLocator;

    public function __construct(Settings $settings, ServiceLocator $settingLocator)
    {
        $this->settings = $settings;
        $this->settingLocator = $settingLocator;
    }

    /**
     * @return void
     */
    public function hooks()
    {
        \add_action('init', [$this, 'registerSettings']);
        \add_action('wp_loaded', [$this, 'enableDeploymentMethod'], 20);
        if (!\is_admin()) {
            return;
        }
        \add_filter('staatic_deployment_methods', [$this, 'registerDeploymentMethod']);
    }

    /**
     * @return void
     */
    public function registerSettings()
    {
        $deployerSettings = [$this->settingLocator->get(ZipfileSetting::class)];
        foreach ($deployerSettings as $setting) {
            $this->settings->addSetting('staatic-deployment', $setting);
        }
    }

    /**
     * @return void
     */
    public function enableDeploymentMethod()
    {
        if (!$this->isSelectedDeploymentMethod()) {
            return;
        }
        \add_filter('staatic_publication_tasks', [$this, 'disableDeploymentTasks']);
        \add_filter('staatic_deployment_strategy', '__return_false');
    }

    private function isSelectedDeploymentMethod() : bool
    {
        return \get_option('staatic_deployment_method') === self::DEPLOYMENT_METHOD_NAME;
    }

    /**
     * @param mixed[] $deploymentMethods
     */
    public function registerDeploymentMethod($deploymentMethods) : array
    {
        $deploymentMethods[self::DEPLOYMENT_METHOD_NAME] = \__('Zipfile', 'staatic');

        return $deploymentMethods;
    }

    /**
     * @param mixed[] $tasks
     */
    public function disableDeploymentTasks($tasks) : array
    {
        unset($tasks[InitiateDeploymentTask::class], $tasks[DeployTask::class], $tasks[FinishDeploymentTask::class]);

        return $tasks;
    }
}
