<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class BucketCannedACL
{
    const AUTHENTICATED_READ = 'authenticated-read';
    const PRIVATE = 'private';
    const PUBLIC_READ = 'public-read';
    const PUBLIC_READ_WRITE = 'public-read-write';
    public static function exists(string $value) : bool
    {
        return isset([self::AUTHENTICATED_READ => \true, self::PRIVATE => \true, self::PUBLIC_READ => \true, self::PUBLIC_READ_WRITE => \true][$value]);
    }
}
