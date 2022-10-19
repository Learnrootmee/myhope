<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Argument;

use Closure;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ServiceLocator as BaseServiceLocator;
class ServiceLocator extends BaseServiceLocator
{
    /**
     * @var Closure
     */
    private $factory;
    /**
     * @var mixed[]
     */
    private $serviceMap;
    /**
     * @var mixed[]|null
     */
    private $serviceTypes;
    public function __construct(Closure $factory, array $serviceMap, array $serviceTypes = null)
    {
        $this->factory = $factory;
        $this->serviceMap = $serviceMap;
        $this->serviceTypes = $serviceTypes;
        parent::__construct($serviceMap);
    }
    /**
     * @return mixed
     * @param string $id
     */
    public function get($id)
    {
        return isset($this->serviceMap[$id]) ? ($this->factory)(...$this->serviceMap[$id]) : parent::get($id);
    }
    public function getProvidedServices() : array
    {
        return $this->serviceTypes ?? ($this->serviceTypes = \array_map(function () {
            return '?';
        }, $this->serviceMap));
    }
}
