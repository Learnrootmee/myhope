<?php

namespace Staatic\Framework\ConfigGenerator;

use Staatic\Framework\Result;
interface ConfigGeneratorInterface
{
    /**
     * @param Result $result
     * @return void
     */
    public function processResult($result);
    public function getFiles() : array;
}
