<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication;

use DateTimeImmutable;
use DateTimeInterface;
use Staatic\Framework\Build;
use Staatic\Framework\Deployment;

final class Publication
{
    /**
     * @var PublicationStatus
     */
    private $status;

    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTimeInterface
     */
    private $dateCreated;

    /**
     * @var Build
     */
    private $build;

    /**
     * @var Deployment
     */
    private $deployment;

    /**
     * @var bool
     */
    private $isPreview = \false;

    /**
     * @var int|null
     */
    private $userId;

    /**
     * @var mixed[]
     */
    private $metadata = [];

    /**
     * @var \DateTimeInterface|null
     */
    private $dateFinished;

    /**
     * @var string|null
     */
    private $currentTask;

    /**
     * @param int|null $userId
     * @param \DateTimeInterface|null $dateFinished
     * @param string|null $currentTask
     */
    public function __construct(
        string $id,
        DateTimeInterface $dateCreated,
        Build $build,
        Deployment $deployment,
        bool $isPreview = \false,
        $userId = null,
        array $metadata = [],
        PublicationStatus $status = null,
        $dateFinished = null,
        $currentTask = null
    )
    {
        $this->id = $id;
        $this->dateCreated = $dateCreated;
        $this->build = $build;
        $this->deployment = $deployment;
        $this->isPreview = $isPreview;
        $this->userId = $userId;
        $this->metadata = $metadata;
        $this->dateFinished = $dateFinished;
        $this->currentTask = $currentTask;
        $this->status = $status ?? PublicationStatus::create(PublicationStatus::STATUS_PENDING);
    }

    public function id() : string
    {
        return $this->id;
    }

    public function dateCreated() : DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function build() : Build
    {
        return $this->build;
    }

    public function deployment() : Deployment
    {
        return $this->deployment;
    }

    public function isPreview() : bool
    {
        return $this->isPreview;
    }

    /**
     * @return int|null
     */
    public function userId()
    {
        return $this->userId;
    }

    /**
     * @return \WP_User|null
     */
    public function publisher()
    {
        if (!$this->userId) {
            return null;
        }

        return \get_userdata($this->userId) ?: null;
    }

    public function metadata() : array
    {
        return $this->metadata;
    }

    public function metadataByKey(string $key)
    {
        return $this->metadata[$key] ?? null;
    }

    public function status() : PublicationStatus
    {
        return $this->status;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function dateFinished()
    {
        return $this->dateFinished;
    }

    /**
     * @return string|null
     */
    public function currentTask()
    {
        return $this->currentTask;
    }

    /**
     * @return void
     */
    public function setStatus(PublicationStatus $status)
    {
        $this->status = $status;
    }

    /**
     * @param string|null $currentTask
     * @return void
     */
    public function setCurrentTask($currentTask)
    {
        $this->currentTask = $currentTask;
    }

    /**
     * @return void
     */
    public function markInProgress()
    {
        $this->status = PublicationStatus::create(PublicationStatus::STATUS_IN_PROGRESS);
    }

    /**
     * @return void
     */
    public function markCanceled()
    {
        $this->currentTask = null;
        $this->status = PublicationStatus::create(PublicationStatus::STATUS_CANCELED);
        $this->dateFinished = new DateTimeImmutable();
    }

    /**
     * @return void
     */
    public function markFailed()
    {
        $this->currentTask = null;
        $this->status = PublicationStatus::create(PublicationStatus::STATUS_FAILED);
        $this->dateFinished = new DateTimeImmutable();
    }

    /**
     * @return void
     */
    public function markFinished()
    {
        $this->currentTask = null;
        $this->status = PublicationStatus::create(PublicationStatus::STATUS_FINISHED);
        $this->dateFinished = new DateTimeImmutable();
    }

    /**
     * @return void
     */
    public function updateMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }
}
