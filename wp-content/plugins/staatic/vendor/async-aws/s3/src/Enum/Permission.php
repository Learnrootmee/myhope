<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class Permission
{
    const FULL_CONTROL = 'FULL_CONTROL';
    const READ = 'READ';
    const READ_ACP = 'READ_ACP';
    const WRITE = 'WRITE';
    const WRITE_ACP = 'WRITE_ACP';
    public static function exists(string $value) : bool
    {
        return isset([self::FULL_CONTROL => \true, self::READ => \true, self::READ_ACP => \true, self::WRITE => \true, self::WRITE_ACP => \true][$value]);
    }
}
