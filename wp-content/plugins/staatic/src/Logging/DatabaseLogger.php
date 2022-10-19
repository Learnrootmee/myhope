<?php

declare(strict_types=1);

namespace Staatic\WordPress\Logging;

use Stringable;
use DateTimeImmutable;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\LoggerTrait;

final class DatabaseLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var LogEntryRepository
     */
    private $repository;

    public function __construct(LogEntryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function log($level, $message, $context = [])
    {
        $this->repository->add(
            new LogEntry($this->repository->nextId(), new DateTimeImmutable(), $level, $message, $context)
        );
    }
}
