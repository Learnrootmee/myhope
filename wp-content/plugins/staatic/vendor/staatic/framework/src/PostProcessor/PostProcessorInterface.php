<?php

namespace Staatic\Framework\PostProcessor;

use Staatic\Framework\Build;
interface PostProcessorInterface
{
    public function createsOrRemovesResults() : bool;
    /**
     * @param Build $build
     * @return void
     */
    public function apply($build);
}
