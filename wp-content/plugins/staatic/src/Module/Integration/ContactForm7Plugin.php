<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Integration;

use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Service\Filesystem;
use WPCF7;

final class ContactForm7Plugin implements ModuleInterface
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return void
     */
    public function hooks()
    {
        \add_action('wp_loaded', [$this, 'setupIntegration']);
    }

    /**
     * @return void
     */
    public function setupIntegration()
    {
        if (!$this->isPluginActive()) {
            return;
        }
        \add_filter('staatic_additional_paths_exclude_paths', [$this, 'overrideAdditionalPathsExcludePaths']);
    }

    /**
     * @param mixed[] $excludePaths
     */
    public function overrideAdditionalPathsExcludePaths($excludePaths) : array
    {
        // see: contact-form-7/includes/functions.php:62
        // contact-form-7/modules/really-simple-captcha.php:441
        $excludePaths[] = $this->filesystem->getUploadsDirectory() . 'wpcf7_captcha';
        $excludePaths[] = $this->filesystem->getUploadsDirectory() . 'wpcf7_uploads';

        return $excludePaths;
    }

    private function isPluginActive() : bool
    {
        if (!\class_exists(WPCF7::class)) {
            return \false;
        }

        return \true;
    }
}
