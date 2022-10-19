<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait ClassTrait
{
    /**
     * @return $this
     * @param string|null $class
     */
    public final function class($class)
    {
        $this->definition->setClass($class);
        return $this;
    }
}
