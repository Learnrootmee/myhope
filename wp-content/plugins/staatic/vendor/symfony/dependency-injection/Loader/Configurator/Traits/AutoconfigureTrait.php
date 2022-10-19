<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
trait AutoconfigureTrait
{
    /**
     * @return $this
     * @param bool $autoconfigured
     */
    public final function autoconfigure($autoconfigured = \true)
    {
        $this->definition->setAutoconfigured($autoconfigured);
        return $this;
    }
}
