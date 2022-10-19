<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting;

interface ActsOnUpdateInterface
{
    /**
     * @return void
     */
    public function onUpdate($value, $valueBefore);
}
