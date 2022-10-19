<?php

namespace Staatic\Framework\ResultRepository;

use Generator;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Framework\Result;
interface ResultRepositoryInterface
{
    public function nextId() : string;
    /**
     * @param Result $result
     * @return void
     */
    public function add($result);
    /**
     * @param Result $result
     * @return void
     */
    public function update($result);
    /**
     * @param Result $result
     * @return void
     */
    public function delete($result);
    /**
     * @param string $sourceBuildId
     * @param string $targetBuildId
     * @return void
     */
    public function mergeBuildResults($sourceBuildId, $targetBuildId);
    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function scheduleForDeployment($buildId, $deploymentId) : int;
    /**
     * @param Result $result
     * @param string $deploymentId
     * @return void
     */
    public function markDeployed($result, $deploymentId);
    /**
     * @param string $resultId
     * @return Result|null
     */
    public function find($resultId);
    public function findAll() : Generator;
    /**
     * @param string $buildId
     */
    public function findByBuildId($buildId) : Generator;
    /**
     * @param string $buildId
     */
    public function findByBuildIdWithRedirectUrl($buildId) : array;
    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function findByBuildIdPendingDeployment($buildId, $deploymentId) : Generator;
    /**
     * @param string $buildId
     * @param UriInterface $url
     * @return Result|null
     */
    public function findOneByBuildIdAndUrl($buildId, $url);
    /**
     * @param string $buildId
     * @param UriInterface $url
     * @return Result|null
     */
    public function findOneByBuildIdAndUrlResolved($buildId, $url);
    /**
     * @param string $buildId
     */
    public function countByBuildId($buildId) : int;
    /**
     * @param string $buildId
     * @param string $deploymentId
     */
    public function countByBuildIdPendingDeployment($buildId, $deploymentId) : int;
}
