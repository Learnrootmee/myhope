<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\LazyProxy\PhpDumper;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
class NullDumper implements DumperInterface
{
    /**
     * @param Definition $definition
     */
    public function isProxyCandidate($definition) : bool
    {
        return \false;
    }
    /**
     * @param Definition $definition
     * @param string $id
     * @param string $factoryCode
     */
    public function getProxyFactoryCode($definition, $id, $factoryCode) : string
    {
        return '';
    }
    /**
     * @param Definition $definition
     */
    public function getProxyCode($definition) : string
    {
        return '';
    }
}
