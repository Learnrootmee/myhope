<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class ObjectLockMode
{
    const COMPLIANCE = 'COMPLIANCE';
    const GOVERNANCE = 'GOVERNANCE';
    public static function exists(string $value) : bool
    {
        return isset([self::COMPLIANCE => \true, self::GOVERNANCE => \true][$value]);
    }
}
