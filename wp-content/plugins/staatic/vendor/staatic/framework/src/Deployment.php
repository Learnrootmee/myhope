<?php

namespace Staatic\Framework;

use DateTimeImmutable;
use DateTimeInterface;
final class Deployment
{
    /**
     * @var \DateTimeInterface
     */
    private $dateCreated;
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $buildId;
    /**
     * @var \DateTimeInterface|null
     */
    private $dateStarted;
    /**
     * @var \DateTimeInterface|null
     */
    private $dateFinished;
    /**
     * @var int
     */
    private $numResultsTotal = 0;
    /**
     * @var int
     */
    private $numResultsDeployable = 0;
    /**
     * @var int
     */
    private $numResultsDeployed = 0;
    /**
     * @var mixed[]|null
     */
    private $metadata;
    /**
     * @param \DateTimeInterface|null $dateCreated
     * @param \DateTimeInterface|null $dateStarted
     * @param \DateTimeInterface|null $dateFinished
     * @param mixed[]|null $metadata
     */
    public function __construct(string $id, string $buildId, $dateCreated = null, $dateStarted = null, $dateFinished = null, int $numResultsTotal = 0, int $numResultsDeployable = 0, int $numResultsDeployed = 0, $metadata = null)
    {
        $this->id = $id;
        $this->buildId = $buildId;
        $this->dateStarted = $dateStarted;
        $this->dateFinished = $dateFinished;
        $this->numResultsTotal = $numResultsTotal;
        $this->numResultsDeployable = $numResultsDeployable;
        $this->numResultsDeployed = $numResultsDeployed;
        $this->metadata = $metadata;
        $this->dateCreated = $dateCreated ?: new DateTimeImmutable();
    }
    public function __toString()
    {
        return (string) $this->id;
    }
    public function id() : string
    {
        return $this->id;
    }
    public function buildId() : string
    {
        return $this->buildId;
    }
    public function dateCreated() : DateTimeInterface
    {
        return $this->dateCreated;
    }
    /**
     * @return \DateTimeInterface|null
     */
    public function dateStarted()
    {
        return $this->dateStarted;
    }
    /**
     * @return \DateTimeInterface|null
     */
    public function dateFinished()
    {
        return $this->dateFinished;
    }
    public function isFinished() : bool
    {
        return (bool) $this->dateFinished;
    }
    public function numResultsTotal() : int
    {
        return $this->numResultsTotal;
    }
    public function numResultsDeployable() : int
    {
        return $this->numResultsDeployable;
    }
    public function numResultsDeployed() : int
    {
        return $this->numResultsDeployed;
    }
    /**
     * @return mixed[]|null
     */
    public function metadata()
    {
        return $this->metadata;
    }
    /**
     * @param int $numResultsTotal
     * @param int $numResultsDeployable
     * @param mixed[]|null $metadata
     * @return void
     */
    public function deployStarted($numResultsTotal, $numResultsDeployable, $metadata)
    {
        $this->dateStarted = new DateTimeImmutable();
        $this->numResultsTotal = $numResultsTotal;
        $this->numResultsDeployable = $numResultsDeployable;
        $this->metadata = $metadata;
    }
    /**
     * @return void
     */
    public function deployFinished()
    {
        $this->dateFinished = new DateTimeImmutable();
    }
    /**
     * @param int $numResultsDeployed
     * @return void
     */
    public function deployedResults($numResultsDeployed)
    {
        $this->numResultsDeployed += $numResultsDeployed;
    }
}
