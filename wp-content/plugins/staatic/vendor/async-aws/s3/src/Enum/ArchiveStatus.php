<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class ArchiveStatus
{
    const ARCHIVE_ACCESS = 'ARCHIVE_ACCESS';
    const DEEP_ARCHIVE_ACCESS = 'DEEP_ARCHIVE_ACCESS';
    public static function exists(string $value) : bool
    {
        return isset([self::ARCHIVE_ACCESS => \true, self::DEEP_ARCHIVE_ACCESS => \true][$value]);
    }
}
