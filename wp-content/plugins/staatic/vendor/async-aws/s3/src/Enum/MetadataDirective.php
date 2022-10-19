<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class MetadataDirective
{
    const COPY = 'COPY';
    const REPLACE = 'REPLACE';
    public static function exists(string $value) : bool
    {
        return isset([self::COPY => \true, self::REPLACE => \true][$value]);
    }
}
