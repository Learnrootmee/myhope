<?php

namespace Staatic\Framework\DeploymentRepository;

use Staatic\Framework\Deployment;
interface DeploymentRepositoryInterface
{
    public function nextId() : string;
    /**
     * @param Deployment $deployment
     * @return void
     */
    public function add($deployment);
    /**
     * @param Deployment $deployment
     * @return void
     */
    public function update($deployment);
    /**
     * @param string $deploymentId
     * @return Deployment|null
     */
    public function find($deploymentId);
}
