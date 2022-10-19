<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait ArgumentTrait
{
    /**
     * @return $this
     * @param mixed[] $arguments
     */
    public final function args($arguments)
    {
        $this->definition->setArguments(static::processValue($arguments, \true));
        return $this;
    }
    /**
     * @param string|int $key
     * @param mixed $value
     * @return $this
     */
    public final function arg($key, $value)
    {
        $this->definition->setArgument($key, static::processValue($value, \true));
        return $this;
    }
}
