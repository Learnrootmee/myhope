<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
class AbortMultipartUploadOutput extends Result
{
    private $requestCharged;
    /**
     * @return string|null
     */
    public function getRequestCharged()
    {
        $this->initialize();
        return $this->requestCharged;
    }
    /**
     * @param Response $response
     * @return void
     */
    protected function populateResult($response)
    {
        $headers = $response->getHeaders();
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
    }
}
