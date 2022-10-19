<?php

namespace Staatic\Vendor\AsyncAws\S3\Enum;

final class ServerSideEncryption
{
    const AES256 = 'AES256';
    const AWS_KMS = 'aws:kms';
    public static function exists(string $value) : bool
    {
        return isset([self::AES256 => \true, self::AWS_KMS => \true][$value]);
    }
}
