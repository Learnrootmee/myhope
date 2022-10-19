<?php

namespace Staatic\Vendor\Psr\Log;

trait LoggerAwareTrait
{
    /**
     * @var LoggerInterface|null
     */
    protected $logger;
    /**
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
}
