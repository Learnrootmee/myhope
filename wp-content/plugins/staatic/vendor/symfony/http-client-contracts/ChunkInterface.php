<?php

namespace Staatic\Vendor\Symfony\Contracts\HttpClient;

use Staatic\Vendor\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
interface ChunkInterface
{
    public function isTimeout() : bool;
    public function isFirst() : bool;
    public function isLast() : bool;
    /**
     * @return mixed[]|null
     */
    public function getInformationalStatus();
    public function getContent() : string;
    public function getOffset() : int;
    /**
     * @return string|null
     */
    public function getError();
}
