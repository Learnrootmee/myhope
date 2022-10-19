<?php

namespace Staatic\Vendor\Symfony\Contracts\Service;

use Staatic\Vendor\Psr\Container\ContainerInterface;
interface ServiceProviderInterface extends ContainerInterface
{
    public function getProvidedServices() : array;
}
