<?php

namespace Staatic\Vendor\Psr\Log;

use Stringable;
interface LoggerInterface
{
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function emergency($message, $context = []);
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function alert($message, $context = []);
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function critical($message, $context = []);
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function error($message, $context = []);
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function warning($message, $context = []);
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function notice($message, $context = []);
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function info($message, $context = []);
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function debug($message, $context = []);
    /**
     * @param string|Stringable $message
     * @param mixed[] $context
     * @return void
     */
    public function log($level, $message, $context = []);
}
