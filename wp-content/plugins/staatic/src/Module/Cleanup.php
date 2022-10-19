<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

use Staatic\WordPress\Logging\LogEntryCleanup;
use Staatic\WordPress\Publication\PublicationCleanup;
use Staatic\WordPress\Service\ResourceCleanup;
use Staatic\WordPress\Service\Scheduler;

final class Cleanup implements ModuleInterface
{
    /**
     * @var Scheduler
     */
    private $scheduler;

    /**
     * @var LogEntryCleanup
     */
    private $logEntryCleanup;

    /**
     * @var PublicationCleanup
     */
    private $publicationCleanup;

    /**
     * @var ResourceCleanup
     */
    private $resourceCleanup;

    /**
     * @var string
     */
    private $cleanupHook;

    /**
     * @var string
     */
    private $cleanupSchedule;

    public function __construct(
        Scheduler $scheduler,
        LogEntryCleanup $logEntryCleanup,
        PublicationCleanup $publicationCleanup,
        ResourceCleanup $resourceCleanup,
        string $cleanupHook,
        string $cleanupSchedule
    )
    {
        $this->scheduler = $scheduler;
        $this->logEntryCleanup = $logEntryCleanup;
        $this->publicationCleanup = $publicationCleanup;
        $this->resourceCleanup = $resourceCleanup;
        $this->cleanupHook = $cleanupHook;
        $this->cleanupSchedule = $cleanupSchedule;
    }

    /**
     * @return void
     */
    public function hooks()
    {
        \add_action($this->cleanupHook, [$this, 'cleanup']);
        \add_action('wp_loaded', [$this, 'setupSchedule']);
    }

    /**
     * @return void
     */
    public function setupSchedule()
    {
        if ($this->scheduler->isScheduled($this->cleanupHook)) {
            return;
        }
        $this->scheduler->schedule($this->cleanupHook, $this->cleanupSchedule);
    }

    /**
     * @return void
     */
    public function cleanup()
    {
        $this->logEntryCleanup->cleanup();
        $this->publicationCleanup->cleanup();
        $this->resourceCleanup->cleanup();
    }
}
