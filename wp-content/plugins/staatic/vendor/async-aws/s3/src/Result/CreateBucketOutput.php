<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
class CreateBucketOutput extends Result
{
    private $location;
    /**
     * @return string|null
     */
    public function getLocation()
    {
        $this->initialize();
        return $this->location;
    }
    /**
     * @param Response $response
     * @return void
     */
    protected function populateResult($response)
    {
        $headers = $response->getHeaders();
        $this->location = $headers['location'][0] ?? null;
    }
}
