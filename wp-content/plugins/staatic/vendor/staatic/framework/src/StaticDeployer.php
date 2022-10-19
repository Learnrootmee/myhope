<?php

namespace Staatic\Framework;

use Generator;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Framework\ResultRepository\ResultRepositoryInterface;
use Staatic\Framework\DeploymentRepository\DeploymentRepositoryInterface;
use Staatic\Framework\DeployStrategy\DeployStrategyInterface;
class StaticDeployer implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var int|null
     */
    private $numResultsDeployable;
    /**
     * @var int|null
     */
    private $numResultsDeployed;
    /**
     * @var DeploymentRepositoryInterface
     */
    private $deploymentRepository;
    /**
     * @var ResultRepositoryInterface
     */
    private $resultRepository;
    /**
     * @var DeployStrategyInterface
     */
    private $deployStrategy;
    /**
     * @param LoggerInterface|null $logger
     */
    public function __construct(DeploymentRepositoryInterface $deploymentRepository, ResultRepositoryInterface $resultRepository, DeployStrategyInterface $deployStrategy, $logger = null)
    {
        $this->deploymentRepository = $deploymentRepository;
        $this->resultRepository = $resultRepository;
        $this->deployStrategy = $deployStrategy;
        $this->logger = $logger ?: new NullLogger();
    }
    /**
     * @param Deployment $deployment
     * @return void
     */
    public function initiateDeployment($deployment)
    {
        $this->logger->notice('Initiating deployment', ['deploymentId' => $deployment->id()]);
        $this->resultRepository->scheduleForDeployment($deployment->buildId(), $deployment->id());
        $deploymentMetadata = $this->deployStrategy->initiate($deployment);
        $numResultsTotal = $this->resultRepository->countByBuildId($deployment->buildId());
        $numResultsDeployable = $this->resultRepository->countByBuildIdPendingDeployment($deployment->buildId(), $deployment->id());
        $deployment->deployStarted($numResultsTotal, $numResultsDeployable, $deploymentMetadata);
        $this->deploymentRepository->update($deployment);
        $this->logger->notice("Deployment initiated ({$numResultsTotal} results total, {$numResultsDeployable} results deployable)", ['deploymentId' => $deployment->id()]);
    }
    /**
     * @param Deployment $deployment
     * @param int|null $numResultsDeployable
     */
    public function processResults($deployment, $numResultsDeployable = null) : bool
    {
        $this->logger->info('Processing results', ['deploymentId' => $deployment->id()]);
        $this->numResultsDeployable = $numResultsDeployable;
        $this->numResultsDeployed = 0;
        $results = $this->resultsPendingDeployment($deployment->buildId(), $deployment->id());
        $this->deployStrategy->processResults($deployment, $results);
        $deployment->deployedResults($this->numResultsDeployed);
        $this->deploymentRepository->update($deployment);
        $this->logger->info("Finished processing {$this->numResultsDeployed} results", ['deploymentId' => $deployment->id()]);
        return $deployment->numResultsDeployable() <= $deployment->numResultsDeployed();
    }
    private function resultsPendingDeployment(string $buildId, string $deploymentId) : Generator
    {
        foreach ($this->resultRepository->findByBuildIdPendingDeployment($buildId, $deploymentId) as $result) {
            (yield $result);
            $this->numResultsDeployed++;
            $this->resultRepository->markDeployed($result, $deploymentId);
            if ($this->numResultsDeployable !== null && $this->numResultsDeployed >= $this->numResultsDeployable) {
                break;
            }
        }
    }
    /**
     * @param Deployment $deployment
     * @return void
     */
    public function finishDeployment($deployment)
    {
        $this->logger->notice('Finishing deployment', ['deploymentId' => $deployment->id()]);
        $this->deployStrategy->finish($deployment);
        $deployment->deployFinished();
        $this->deploymentRepository->update($deployment);
        $this->logger->notice('Finished deployment', ['deploymentId' => $deployment->id()]);
    }
}
