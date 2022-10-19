<?php

declare(strict_types=1);

namespace Staatic\WordPress;

use Staatic\WordPress\Migrations\MigrationCoordinatorFactory;

final class Activator
{
    const NAMESPACE = 'staatic';

    /**
     * @var MigrationCoordinatorFactory
     */
    private $coordinatorFactory;

    public function __construct(MigrationCoordinatorFactory $coordinatorFactory)
    {
        $this->coordinatorFactory = $coordinatorFactory;
    }

    /**
     * @return void
     */
    public function activate()
    {
        $coordinator = ($this->coordinatorFactory)(self::NAMESPACE);
        if ($coordinator->shouldMigrate()) {
            $coordinator->migrate();
        }
    }
}
