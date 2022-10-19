<?php

namespace Staatic\Framework\ResourceRepository;

use Staatic\Framework\Resource;
interface ResourceRepositoryInterface
{
    /**
     * @param Resource $resource
     * @return void
     */
    public function write($resource);
    /**
     * @param string $sha1
     * @return Resource|null
     */
    public function find($sha1);
    /**
     * @param string $sha1
     * @return void
     */
    public function delete($sha1);
}
