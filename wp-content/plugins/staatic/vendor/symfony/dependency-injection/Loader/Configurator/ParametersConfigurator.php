<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\Expression;
class ParametersConfigurator extends AbstractConfigurator
{
    const FACTORY = 'parameters';
    private $container;
    public function __construct(ContainerBuilder $container)
    {
        $this->container = $container;
    }
    /**
     * @param mixed $value
     * @return $this
     * @param string $name
     */
    public final function set($name, $value)
    {
        if ($value instanceof Expression) {
            throw new InvalidArgumentException(\sprintf('Using an expression in parameter "%s" is not allowed.', $name));
        }
        $this->container->setParameter($name, static::processValue($value, \true));
        return $this;
    }
    /**
     * @param mixed $value
     * @return $this
     */
    public final function __invoke(string $name, $value)
    {
        return $this->set($name, $value);
    }
}
