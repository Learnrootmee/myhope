<?php

namespace Staatic\Vendor\Symfony\Component\HttpClient\Chunk;

class InformationalChunk extends DataChunk
{
    /**
     * @var mixed[]
     */
    private $status;
    public function __construct(int $statusCode, array $headers)
    {
        $this->status = [$statusCode, $headers];
    }
    /**
     * @return mixed[]|null
     */
    public function getInformationalStatus()
    {
        return $this->status;
    }
}
