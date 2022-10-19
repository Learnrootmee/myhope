<?php

namespace Staatic\Vendor\AsyncAws\S3\ValueObject;

final class DeletedObject
{
    private $key;
    private $versionId;
    private $deleteMarker;
    private $deleteMarkerVersionId;
    public function __construct(array $input)
    {
        $this->key = $input['Key'] ?? null;
        $this->versionId = $input['VersionId'] ?? null;
        $this->deleteMarker = $input['DeleteMarker'] ?? null;
        $this->deleteMarkerVersionId = $input['DeleteMarkerVersionId'] ?? null;
    }
    public static function create($input) : self
    {
        return $input instanceof self ? $input : new self($input);
    }
    /**
     * @return bool|null
     */
    public function getDeleteMarker()
    {
        return $this->deleteMarker;
    }
    /**
     * @return string|null
     */
    public function getDeleteMarkerVersionId()
    {
        return $this->deleteMarkerVersionId;
    }
    /**
     * @return string|null
     */
    public function getKey()
    {
        return $this->key;
    }
    /**
     * @return string|null
     */
    public function getVersionId()
    {
        return $this->versionId;
    }
}
