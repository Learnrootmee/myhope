<?php

namespace Staatic\Vendor\Psr\Log;

interface LoggerAwareInterface
{
    /**
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger($logger);
}
