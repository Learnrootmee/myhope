<?php

namespace Staatic\Crawler\UrlExtractor;

interface TransformableInterface
{
    /**
     * @param callable|null $callback
     * @return void
     */
    public function setTransformCallback($callback);
}
