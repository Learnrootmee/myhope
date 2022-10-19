<?php

declare(strict_types=1);

namespace Staatic\WordPress\Logging;

final class LogEntryCleanup
{
    /** @var int */
    const CLEANUP_AFTER_NUM_DAYS = 7;

    /**
     * @var LogEntryRepository
     */
    private $logEntryRepository;

    public function __construct(LogEntryRepository $logEntryRepository)
    {
        $this->logEntryRepository = $logEntryRepository;
    }

    /**
     * @return void
     */
    public function cleanup()
    {
        $excludePublicationIds = \array_filter([
            \get_option('staatic_current_publication_id'),
            \get_option('staatic_latest_publication_id'),
            \get_option('staatic_active_publication_id'),
            \get_option('staatic_active_preview_publication_id')
        ]);
        $this->logEntryRepository->deleteOlderThan(self::CLEANUP_AFTER_NUM_DAYS, $excludePublicationIds);
    }
}
