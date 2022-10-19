<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Advanced;

use Staatic\WordPress\Service\Filesystem;
use Staatic\WordPress\Setting\AbstractSetting;

final class WorkDirectorySetting extends AbstractSetting
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
        return 'staatic_work_directory';
    }

    public function type() : string
    {
        return self::TYPE_STRING;
    }

    public function label() : string
    {
        return \__('Work Directory', 'staatic');
    }

    /**
     * @return string|null
     */
    public function description()
    {
        return \__('Temporary files created during publications are stored in this directory.', 'staatic');
    }

    public function defaultValue()
    {
        return $this->filesystem->getUploadsDirectory() . 'staatic/';
    }
}
