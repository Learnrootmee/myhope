<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Deployer\FilesystemDeployer;

use Staatic\WordPress\Service\Filesystem;
use Staatic\WordPress\Setting\AbstractSetting;

final class TargetDirectorySetting extends AbstractSetting
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function name() : string
    {
        return 'staatic_filesystem_target_directory';
    }

    public function type() : string
    {
        return self::TYPE_STRING;
    }

    public function label() : string
    {
        return \__('Target Directory', 'staatic');
    }

    /**
     * @return string|null
     */
    public function description()
    {
        return \__('The path to the directory on the filesystem where the static version of your site is deployed.', 'staatic');
    }

    public function defaultValue()
    {
        return $this->filesystem->getUploadsDirectory() . 'staatic/deploy/';
    }
}
