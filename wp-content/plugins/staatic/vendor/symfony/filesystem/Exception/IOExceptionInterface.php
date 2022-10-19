<?php

namespace Staatic\Vendor\Symfony\Component\Filesystem\Exception;

interface IOExceptionInterface extends ExceptionInterface
{
    /**
     * @return string|null
     */
    public function getPath();
}
