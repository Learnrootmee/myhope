<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class ObjectLockLegalHoldStatus
{
    const OFF = 'OFF';
    const ON = 'ON';
    public static function exists(string $value) : bool
    {
        return isset([self::OFF => \true, self::ON => \true][$value]);
    }
}
