<?php

namespace Staatic\Vendor\GuzzleHttp\Handler;

use Staatic\Vendor\Psr\Http\Message\RequestInterface;
interface CurlFactoryInterface
{
    /**
     * @param RequestInterface $request
     * @param mixed[] $options
     */
    public function create($request, $options) : EasyHandle;
    /**
     * @param EasyHandle $easy
     * @return void
     */
    public function release($easy);
}
