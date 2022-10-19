<?php

namespace Staatic\Framework\DeploymentRepository;

use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use Staatic\Framework\Deployment;
final class InMemoryDeploymentRepository implements DeploymentRepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var mixed[]
     */
    private $deployments = [];
    public function __construct()
    {
        $this->logger = new NullLogger();
    }
    public function nextId() : string
    {
        return (string) Uuid::uuid4();
    }
    /**
     * @param Deployment $deployment
     * @return void
     */
    public function add($deployment)
    {
        $this->logger->debug("Adding deployment #{$deployment->id()}", ['deploymentId' => $deployment->id()]);
        $this->deployments[$deployment->id()] = $deployment;
    }
    /**
     * @param Deployment $deployment
     * @return void
     */
    public function update($deployment)
    {
        $this->logger->debug("Updating deployment #{$deployment->id()}", ['deploymentId' => $deployment->id()]);
        $this->deployments[$deployment->id()] = $deployment;
    }
    /**
     * @param string $deploymentId
     * @return Deployment|null
     */
    public function find($deploymentId)
    {
        return $this->deployments[$deploymentId] ?? null;
    }
}
