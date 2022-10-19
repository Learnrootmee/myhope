<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class RequestCharged
{
    const REQUESTER = 'requester';
    public static function exists(string $value) : bool
    {
        return isset([self::REQUESTER => \true][$value]);
    }
}
