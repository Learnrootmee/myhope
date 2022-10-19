<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class ObjectOwnership
{
    const BUCKET_OWNER_ENFORCED = 'BucketOwnerEnforced';
    const BUCKET_OWNER_PREFERRED = 'BucketOwnerPreferred';
    const OBJECT_WRITER = 'ObjectWriter';
    public static function exists(string $value) : bool
    {
        return isset([self::BUCKET_OWNER_ENFORCED => \true, self::BUCKET_OWNER_PREFERRED => \true, self::OBJECT_WRITER => \true][$value]);
    }
}
