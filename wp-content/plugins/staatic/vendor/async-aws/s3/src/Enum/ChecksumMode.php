<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class ChecksumMode
{
    const ENABLED = 'ENABLED';
    public static function exists(string $value) : bool
    {
        return isset([self::ENABLED => \true][$value]);
    }
}
