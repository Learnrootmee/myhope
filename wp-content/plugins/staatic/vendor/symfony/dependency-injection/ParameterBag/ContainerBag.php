<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\ParameterBag;

use UnitEnum;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Container;
class ContainerBag extends FrozenParameterBag implements ContainerBagInterface
{
    private $container;
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    public function all() : array
    {
        return $this->container->getParameterBag()->all();
    }
    /**
     * @return mixed[]|bool|string|int|float|UnitEnum|null
     * @param string $name
     */
    public function get($name)
    {
        return $this->container->getParameter($name);
    }
    /**
     * @param string $name
     */
    public function has($name) : bool
    {
        return $this->container->hasParameter($name);
    }
}
