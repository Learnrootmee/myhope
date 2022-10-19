<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

trait ContainerAwareTrait
{
    protected $container;
    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer($container = null)
    {
        $this->container = $container;
    }
}
