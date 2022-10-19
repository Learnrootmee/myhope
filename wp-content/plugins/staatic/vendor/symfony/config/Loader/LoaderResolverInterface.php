<?php

namespace Staatic\Vendor\Symfony\Component\Config\Loader;

interface LoaderResolverInterface
{
    /**
     * @return LoaderInterface|true
     * @param mixed $resource
     * @param string|null $type
     */
    public function resolve($resource, $type = null);
}
