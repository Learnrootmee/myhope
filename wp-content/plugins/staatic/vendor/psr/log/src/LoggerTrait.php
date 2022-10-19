<?php

namespace Staatic\Vendor\Psr\Log;

use Stringable;
trait LoggerTrait
{
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function emergency($message, $context = [])
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function alert($message, $context = [])
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function critical($message, $context = [])
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function error($message, $context = [])
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function warning($message, $context = [])
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function notice($message, $context = [])
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function info($message, $context = [])
    {
        $this->log(LogLevel::INFO, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function debug($message, $context = [])
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public abstract function log($level, $message, $context = []);
}
