<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait SyntheticTrait
{
    /**
     * @return $this
     * @param bool $synthetic
     */
    public final function synthetic($synthetic = \true)
    {
        $this->definition->setSynthetic($synthetic);
        return $this;
    }
}
