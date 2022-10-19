<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Math;

final class RoundingMode
{
    private function __construct()
    {
    }
    const UNNECESSARY = 0;
    const UP = 1;
    const DOWN = 2;
    const CEILING = 3;
    const FLOOR = 4;
    const HALF_UP = 5;
    const HALF_DOWN = 6;
    const HALF_CEILING = 7;
    const HALF_FLOOR = 8;
    const HALF_EVEN = 9;
}
