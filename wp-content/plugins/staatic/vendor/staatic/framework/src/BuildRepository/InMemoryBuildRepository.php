<?php

namespace Staatic\Framework\BuildRepository;

use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Vendor\Ramsey\Uuid\Uuid;
use Staatic\Framework\Build;
final class InMemoryBuildRepository implements BuildRepositoryInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    /**
     * @var mixed[]
     */
    private $builds = [];
    public function __construct()
    {
        $this->logger = new NullLogger();
    }
    public function nextId() : string
    {
        return (string) Uuid::uuid4();
    }
    /**
     * @param Build $build
     * @return void
     */
    public function add($build)
    {
        $this->logger->debug("Adding build #{$build->id()}", ['buildId' => $build->id()]);
        $this->builds[$build->id()] = $build;
    }
    /**
     * @param Build $build
     * @return void
     */
    public function update($build)
    {
        $this->logger->debug("Updating build #{$build->id()}", ['buildId' => $build->id()]);
        $this->builds[$build->id()] = $build;
    }
    /**
     * @param string $buildId
     * @return Build|null
     */
    public function find($buildId)
    {
        return $this->builds[$buildId] ?? null;
    }
}
