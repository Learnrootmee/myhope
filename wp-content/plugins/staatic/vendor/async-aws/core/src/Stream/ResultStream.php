<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core\Stream;

interface ResultStream
{
    /**
     * @return mixed[]
     */
    public function getChunks();
    public function getContentAsString() : string;
    public function getContentAsResource();
}
