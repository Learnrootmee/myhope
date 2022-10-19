<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Dumper;

interface DumperInterface
{
    /**
     * @return string|mixed[]
     * @param mixed[] $options
     */
    public function dump($options = []);
}
