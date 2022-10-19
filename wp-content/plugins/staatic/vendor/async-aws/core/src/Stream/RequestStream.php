<?php

namespace Staatic\Vendor\AsyncAws\Core\Stream;

use IteratorAggregate;
interface RequestStream extends IteratorAggregate
{
    /**
     * @return int|null
     */
    public function length();
    public function stringify() : string;
    /**
     * @param string $algo
     * @param bool $raw
     */
    public function hash($algo = 'sha256', $raw = \false) : string;
}
