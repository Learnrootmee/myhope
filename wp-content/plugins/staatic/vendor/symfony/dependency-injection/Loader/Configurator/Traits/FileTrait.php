<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

trait FileTrait
{
    /**
     * @return $this
     * @param string $file
     */
    public final function file($file)
    {
        $this->definition->setFile($file);
        return $this;
    }
}
