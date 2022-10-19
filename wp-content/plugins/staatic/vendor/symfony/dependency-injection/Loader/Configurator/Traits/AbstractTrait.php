<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait AbstractTrait
{
    /**
     * @return $this
     * @param bool $abstract
     */
    public final function abstract($abstract = \true)
    {
        $this->definition->setAbstract($abstract);
        return $this;
    }
}
