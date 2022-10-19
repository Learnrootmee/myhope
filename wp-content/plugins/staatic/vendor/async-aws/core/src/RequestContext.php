<?php

namespace Staatic\Vendor\AsyncAws\Core;

use DateTimeImmutable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
class RequestContext
{
    const AVAILABLE_OPTIONS = ['region' => \true, 'operation' => \true, 'expirationDate' => \true, 'currentDate' => \true, 'exceptionMapping' => \true, 'usesEndpointDiscovery' => \true, 'requiresEndpointDiscovery' => \true];
    private $operation;
    private $usesEndpointDiscovery = \false;
    private $requiresEndpointDiscovery = \false;
    private $region;
    private $expirationDate;
    private $currentDate;
    private $exceptionMapping = [];
    public function __construct(array $options = [])
    {
        if (0 < \count($invalidOptions = \array_diff_key($options, self::AVAILABLE_OPTIONS))) {
            throw new InvalidArgument(\sprintf('Invalid option(s) "%s" passed to "%s". ', \implode('", "', \array_keys($invalidOptions)), __METHOD__));
        }
        foreach ($options as $property => $value) {
            $this->{$property} = $value;
        }
    }
    /**
     * @return string|null
     */
    public function getOperation()
    {
        return $this->operation;
    }
    /**
     * @return string|null
     */
    public function getRegion()
    {
        return $this->region;
    }
    /**
     * @return DateTimeImmutable|null
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }
    /**
     * @return DateTimeImmutable|null
     */
    public function getCurrentDate()
    {
        return $this->currentDate;
    }
    public function getExceptionMapping() : array
    {
        return $this->exceptionMapping;
    }
    public function usesEndpointDiscovery() : bool
    {
        return $this->usesEndpointDiscovery;
    }
    public function requiresEndpointDiscovery() : bool
    {
        return $this->requiresEndpointDiscovery;
    }
}
