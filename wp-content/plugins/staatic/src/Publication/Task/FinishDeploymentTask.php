<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication\Task;

use Staatic\WordPress\Factory\StaticDeployerFactory;
use Staatic\WordPress\Publication\Publication;

final class FinishDeploymentTask implements TaskInterface
{
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
        return 'finish_deployment';
    }

    public function description() : string
    {
        return \__('Finishing deployment', 'staatic');
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
        $staticDeployer->finishDeployment($publication->deployment());

        return \true;
    }
}
