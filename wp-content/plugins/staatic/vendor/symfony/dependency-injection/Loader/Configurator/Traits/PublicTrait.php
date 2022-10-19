<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait PublicTrait
{
    /**
     * @return $this
     */
    public final function public()
    {
        $this->definition->setPublic(\true);
        return $this;
    }
    /**
     * @return $this
     */
    public final function private()
    {
        $this->definition->setPublic(\false);
        return $this;
    }
}
