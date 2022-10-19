<?php

namespace Staatic\Vendor\Psr\Http\Message;

interface RequestInterface extends MessageInterface
{
    public function getRequestTarget();
    public function withRequestTarget($requestTarget);
    public function getMethod();
    public function withMethod($method);
    public function getUri();
    /**
     * @param UriInterface $uri
     */
    public function withUri($uri, $preserveHost = \false);
}
