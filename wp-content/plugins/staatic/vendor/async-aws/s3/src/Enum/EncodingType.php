<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class EncodingType
{
    const URL = 'url';
    public static function exists(string $value) : bool
    {
        return isset([self::URL => \true][$value]);
    }
}
