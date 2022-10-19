<?php

declare(strict_types=1);

namespace Staatic\WordPress\Logging;

interface Contextable
{
    /**
     * @param mixed[] $context
     * @return void
     */
    public function changeContext($context);
}
