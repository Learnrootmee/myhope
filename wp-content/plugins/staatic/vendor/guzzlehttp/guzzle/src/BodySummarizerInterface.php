<?php

namespace Staatic\Vendor\GuzzleHttp;

use Staatic\Vendor\Psr\Http\Message\MessageInterface;
interface BodySummarizerInterface
{
    /**
     * @param MessageInterface $message
     * @return string|null
     */
    public function summarize($message);
}
