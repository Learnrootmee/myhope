<?php

namespace Staatic\Vendor\AsyncAws\Core;

abstract class Input
{
    public $region;
    protected function __construct(array $input)
    {
        $this->region = $input['@region'] ?? null;
    }
    /**
     * @param string|null $region
     * @return void
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }
    /**
     * @return string|null
     */
    public function getRegion()
    {
        return $this->region;
    }
    public abstract function request() : Request;
}
