<?php

namespace Staatic\Vendor\GuzzleHttp\Promise;

interface TaskQueueInterface
{
    public function isEmpty();
    /**
     * @param callable $task
     */
    public function add($task);
    public function run();
}
