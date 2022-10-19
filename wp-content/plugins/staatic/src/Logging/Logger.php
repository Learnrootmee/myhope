<?php

declare(strict_types=1);

namespace Staatic\WordPress\Logging;

use Stringable;
use Staatic\Vendor\Psr\Log\LoggerInterface as PsrLoggerInterface;
use Staatic\Vendor\Psr\Log\LogLevel;
use Staatic\Framework\Logger\LoggerTrait;
use Staatic\WordPress\Bootstrap;

final class Logger implements \Staatic\WordPress\Logging\LoggerInterface
{
    use LoggerTrait;

    const LOG_LEVELS_ORDERED = [
        LogLevel::DEBUG,
        LogLevel::INFO,
        LogLevel::NOTICE,
        LogLevel::WARNING,
        LogLevel::ERROR,
        LogLevel::CRITICAL,
        LogLevel::ALERT,
        LogLevel::EMERGENCY
    ];

    /**
     * @var int
     */
    private $minimumLevelIndex;

    /**
     * @var mixed[]
     */
    private $context = [];

    /**
     * @var PsrLoggerInterface
     */
    private $databaseLogger;

    /**
     * @var PsrLoggerInterface
     */
    private $consoleLogger;

    /**
     * @var bool
     */
    private $consoleLoggerEnabled = \false;

    public function __construct(
        PsrLoggerInterface $databaseLogger,
        PsrLoggerInterface $consoleLogger,
        string $minimumLevel = LogLevel::DEBUG,
        bool $consoleLoggerEnabled = \false
    )
    {
        $this->databaseLogger = $databaseLogger;
        $this->consoleLogger = $consoleLogger;
        $this->consoleLoggerEnabled = $consoleLoggerEnabled;
        $this->minimumLevelIndex = \array_search($minimumLevel, self::LOG_LEVELS_ORDERED);
    }

    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function log($level, $message, $context = array())
    {
        if (!$this->shouldHandle($level)) {
            return;
        }
        $context = \array_merge($this->context, $context);
        if (Bootstrap::instance()->isDebug()) {
            $context = \array_merge($this->getSourceContext(), [
                'memory' => \memory_get_usage()
            ], $context);
        }
        $this->databaseLogger->log($level, $message, $context);
        if ($this->consoleLoggerEnabled) {
            $this->consoleLogger->log($level, $message, $context);
        }
    }

    private function shouldHandle($level) : bool
    {
        $levelIndex = \array_search($level, self::LOG_LEVELS_ORDERED);

        return $levelIndex >= $this->minimumLevelIndex;
    }

    public function consoleLoggerEnabled() : bool
    {
        return $this->consoleLoggerEnabled;
    }

    /**
     * @return void
     */
    public function enableConsoleLogger()
    {
        $this->consoleLoggerEnabled = \true;
    }

    /**
     * @return void
     */
    public function disableConsoleLogger()
    {
        $this->consoleLoggerEnabled = \false;
    }

    /**
     * @param mixed[] $context
     * @return void
     */
    public function changeContext($context)
    {
        $this->context = $context;
    }
}
