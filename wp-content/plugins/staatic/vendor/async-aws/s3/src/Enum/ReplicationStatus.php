<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class ReplicationStatus
{
    const COMPLETE = 'COMPLETE';
    const FAILED = 'FAILED';
    const PENDING = 'PENDING';
    const REPLICA = 'REPLICA';
    public static function exists(string $value) : bool
    {
        return isset([self::COMPLETE => \true, self::FAILED => \true, self::PENDING => \true, self::REPLICA => \true][$value]);
    }
}
