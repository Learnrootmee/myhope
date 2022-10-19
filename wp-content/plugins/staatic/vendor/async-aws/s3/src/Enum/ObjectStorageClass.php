<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class ObjectStorageClass
{
    const DEEP_ARCHIVE = 'DEEP_ARCHIVE';
    const GLACIER = 'GLACIER';
    const GLACIER_IR = 'GLACIER_IR';
    const INTELLIGENT_TIERING = 'INTELLIGENT_TIERING';
    const ONEZONE_IA = 'ONEZONE_IA';
    const OUTPOSTS = 'OUTPOSTS';
    const REDUCED_REDUNDANCY = 'REDUCED_REDUNDANCY';
    const STANDARD = 'STANDARD';
    const STANDARD_IA = 'STANDARD_IA';
    public static function exists(string $value) : bool
    {
        return isset([self::DEEP_ARCHIVE => \true, self::GLACIER => \true, self::GLACIER_IR => \true, self::INTELLIGENT_TIERING => \true, self::ONEZONE_IA => \true, self::OUTPOSTS => \true, self::REDUCED_REDUNDANCY => \true, self::STANDARD => \true, self::STANDARD_IA => \true][$value]);
    }
}
