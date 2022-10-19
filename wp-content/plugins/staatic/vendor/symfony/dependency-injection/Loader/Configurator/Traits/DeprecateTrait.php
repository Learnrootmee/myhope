<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
trait DeprecateTrait
{
    /**
     * @return $this
     * @param string $package
     * @param string $version
     * @param string $message
     */
    public final function deprecate($package, $version, $message)
    {
        $this->definition->setDeprecated($package, $version, $message);
        return $this;
    }
}
