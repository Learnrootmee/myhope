<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication\Task;

use Staatic\WordPress\Factory\StaticDeployerFactory;
use Staatic\WordPress\Publication\Publication;

final class DeployTask implements TaskInterface
{
    /** @var int */
    const PROCESS_BATCH_SIZE = 25;

    /**
     * @var StaticDeployerFactory
     */
    private $factory;

    public function __construct(StaticDeployerFactory $factory)
    {
        $this->factory = $factory;
    }

    public function name() : string
    {
        return 'deploy';
    }

    public function description() : string
    {
        return \__('Deploying WordPress site', 'staatic');
    }

    /**
     * @param Publication $publication
     */
    public function supports($publication) : bool
    {
        return \true;
    }

    /**
     * @param Publication $publication
     */
    public function execute($publication) : bool
    {
        $staticDeployer = ($this->factory)($publication);
        $deployFinished = $staticDeployer->processResults($publication->deployment(), self::PROCESS_BATCH_SIZE);

        return $deployFinished;
    }
}
