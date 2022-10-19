<?php

namespace Staatic\Vendor\Psr\Cache;

use DateTimeInterface;
use DateInterval;
interface CacheItemInterface
{
    public function getKey() : string;
    /**
     * @return mixed
     */
    public function get();
    public function isHit() : bool;
    /**
     * @param mixed $value
     * @return $this
     */
    public function set($value);
    /**
     * @return $this
     * @param DateTimeInterface|null $expiration
     */
    public function expiresAt($expiration);
    /**
     * @param int|DateInterval|null $time
     * @return $this
     */
    public function expiresAfter($time);
}
