<?php

namespace Staatic\Framework\DeployStrategy;

use Staatic\Framework\Deployment;
interface DeployStrategyInterface
{
    /**
     * @param Deployment $deployment
     */
    public function initiate($deployment) : array;
    /**
     * @param Deployment $deployment
     * @param mixed[] $results
     * @return void
     */
    public function processResults($deployment, $results);
    /**
     * @param Deployment $deployment
     * @return void
     */
    public function finish($deployment);
}
