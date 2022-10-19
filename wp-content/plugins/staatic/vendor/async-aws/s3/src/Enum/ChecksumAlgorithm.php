<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class ChecksumAlgorithm
{
    const CRC32 = 'CRC32';
    const CRC32C = 'CRC32C';
    const SHA1 = 'SHA1';
    const SHA256 = 'SHA256';
    public static function exists(string $value) : bool
    {
        return isset([self::CRC32 => \true, self::CRC32C => \true, self::SHA1 => \true, self::SHA256 => \true][$value]);
    }
}
