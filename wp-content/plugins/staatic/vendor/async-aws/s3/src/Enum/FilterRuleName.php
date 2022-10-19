<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class FilterRuleName
{
    const PREFIX = 'prefix';
    const SUFFIX = 'suffix';
    public static function exists(string $value) : bool
    {
        return isset([self::PREFIX => \true, self::SUFFIX => \true][$value]);
    }
}
