<?php

namespace Staatic\Framework\BuildRepository;

use Staatic\Framework\Build;
interface BuildRepositoryInterface
{
    public function nextId() : string;
    /**
     * @param Build $build
     * @return void
     */
    public function add($build);
    /**
     * @param Build $build
     * @return void
     */
    public function update($build);
    /**
     * @param string $buildId
     * @return Build|null
     */
    public function find($buildId);
}
