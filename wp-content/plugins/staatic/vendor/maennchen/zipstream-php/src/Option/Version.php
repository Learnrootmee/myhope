<?php

declare (strict_types=1);
namespace Staatic\Vendor\ZipStream\Option;

use Staatic\Vendor\MyCLabs\Enum\Enum;
class Version extends Enum
{
    const STORE = 0xa;
    const DEFLATE = 0x14;
    const ZIP64 = 0x2d;
}
