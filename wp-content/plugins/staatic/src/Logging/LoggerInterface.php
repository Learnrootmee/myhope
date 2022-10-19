<?php

declare(strict_types=1);

namespace Staatic\WordPress\Logging;

use Staatic\Vendor\Psr\Log\LoggerInterface as PsrLoggerInterface;

interface LoggerInterface extends PsrLoggerInterface, Contextable
{
    public function consoleLoggerEnabled() : bool;

    /**
     * @return void
     */
    public function enableConsoleLogger();

    /**
     * @return void
     */
    public function disableConsoleLogger();
}
