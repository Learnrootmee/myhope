<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait PropertyTrait
{
    /**
     * @param mixed $value
     * @return $this
     * @param string $name
     */
    public final function property($name, $value)
    {
        $this->definition->setProperty($name, static::processValue($value, \true));
        return $this;
    }
}
