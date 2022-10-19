<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait ShareTrait
{
    /**
     * @return $this
     * @param bool $shared
     */
    public final function share($shared = \true)
    {
        $this->definition->setShared($shared);
        return $this;
    }
}
