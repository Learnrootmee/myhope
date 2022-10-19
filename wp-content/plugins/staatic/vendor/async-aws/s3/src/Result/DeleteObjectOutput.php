<?php

namespace Staatic\Vendor\AsyncAws\S3\Result;

use Staatic\Vendor\AsyncAws\Core\Response;
use Staatic\Vendor\AsyncAws\Core\Result;
use Staatic\Vendor\AsyncAws\S3\Enum\RequestCharged;
class DeleteObjectOutput extends Result
{
    private $deleteMarker;
    private $versionId;
    private $requestCharged;
    /**
     * @return bool|null
     */
    public function getDeleteMarker()
    {
        $this->initialize();
        return $this->deleteMarker;
    }
    /**
     * @return string|null
     */
    public function getRequestCharged()
    {
        $this->initialize();
        return $this->requestCharged;
    }
    /**
     * @return string|null
     */
    public function getVersionId()
    {
        $this->initialize();
        return $this->versionId;
    }
    /**
     * @param Response $response
     * @return void
     */
    protected function populateResult($response)
    {
        $headers = $response->getHeaders();
        $this->deleteMarker = isset($headers['x-amz-delete-marker'][0]) ? \filter_var($headers['x-amz-delete-marker'][0], \FILTER_VALIDATE_BOOLEAN) : null;
        $this->versionId = $headers['x-amz-version-id'][0] ?? null;
        $this->requestCharged = $headers['x-amz-request-charged'][0] ?? null;
    }
}
