<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait AutowireTrait
{
    /**
     * @return $this
     * @param bool $autowired
     */
    public final function autowire($autowired = \true)
    {
        $this->definition->setAutowired($autowired);
        return $this;
    }
}
