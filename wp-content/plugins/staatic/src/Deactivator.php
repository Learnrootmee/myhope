<?php

declare(strict_types=1);

namespace Staatic\WordPress;

use Staatic\WordPress\Service\Scheduler;

final class Deactivator
{
    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * @var string
     */
    private $cleanupHook;

    public function __construct(Scheduler $scheduler, string $cleanupHook)
    {
        $this->scheduler = $scheduler;
        $this->cleanupHook = $cleanupHook;
    }

    /**
     * @return void
     */
    public function deactivate()
    {
        $this->unscheduleEvents();
    }

    /**
     * @return void
     */
    private function unscheduleEvents()
    {
        if ($this->scheduler->isScheduled($this->cleanupHook)) {
            $this->scheduler->unschedule($this->cleanupHook);
        }
    }
}
