<?php

declare(strict_types=1);

namespace Staatic\WordPress\Publication;

use Error;
use Exception;
use Staatic\Vendor\Psr\Log\LoggerInterface;
use Staatic\WordPress\Bootstrap;
use Staatic\WordPress\Logging\Contextable;
use Staatic\Vendor\WP_Background_Process;

final class BackgroundPublisher extends WP_Background_Process
{
    /** @var string */
    protected $prefix = 'staatic';

    /** @var string */
    protected $action = 'background_publisher';

    /** @var int */
    protected $cron_interval = 1;

    // Execute as quickly as possible...
    /**
     * @var bool
     */
    private $debug;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var PublicationTaskProvider
     */
    private $publicationTaskProvider;

    public function __construct(
        LoggerInterface $logger,
        PublicationRepository $publicationRepository,
        PublicationTaskProvider $publicationTaskProvider
    )
    {
        $this->logger = $logger;
        $this->publicationRepository = $publicationRepository;
        $this->publicationTaskProvider = $publicationTaskProvider;
        if (\is_multisite()) {
            $this->prefix = \sprintf('%d_%s', \get_current_blog_id(), $this->prefix);
        }
        parent::__construct();
        $this->debug = Bootstrap::instance()->isDebug();
    }

    /**
     * @param Publication $publication
     */
    public function initiate($publication) : bool
    {
        if (!$publication->status()->isPending()) {
            $this->logger->notice(\sprintf(
                /* translators: %s: Publication ID. */
                \__('Ignoring publication #%s; publication already started', 'staatic'),
                $publication->id()
            ));

            return \false;
        }
        $this->logger->notice(\__('Starting publication', 'staatic'), [
            'publicationId' => $publication->id()
        ]);
        $firstTask = $this->publicationTaskProvider->firstTask();
        $publication->markInProgress();
        $this->publicationRepository->update($publication);
        $this->push_to_queue(\get_class($firstTask))->save()->dispatch();

        return \true;
    }

    /**
     * @param Publication $publication
     */
    public function cancel($publication) : bool
    {
        $currentPublicationId = \get_option('staatic_current_publication_id');
        if (!$currentPublicationId || $currentPublicationId !== $publication->id()) {
            $this->logger->warning(\__('Cannot cancel publication; publication has already finished', 'staatic'));

            return \false;
        }
        $this->logger->notice(\__('Canceling publication', 'staatic'), [
            'publicationId' => $currentPublicationId
        ]);
        $this->cancel_process();
        $publication->markCanceled();
        $this->publicationRepository->update($publication);
        \update_option('staatic_current_publication_id', '');

        return \true;
    }

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task($taskName)
    {
        $publicationId = \get_option('staatic_current_publication_id');
        if (!$publicationId) {
            $this->logger->critical(\sprintf(
                /* translators: %s: Publication task. */
                \__('Current publication is unknown during task %s', 'staatic'),
                $taskName
            ));

            return \false;
        }
        $task = $this->publicationTaskProvider->getTask($taskName);
        if ($this->logger instanceof Contextable) {
            $this->logger->changeContext([
                'publicationId' => $publicationId,
                'task' => $taskName
            ]);
        }
        $publication = $this->publicationRepository->find($publicationId);
        if ($publication === null) {
            $this->logger->critical(\sprintf(
                /* translators: %s: Publication ID, %2$s: Publication task. */
                \__('Unable to find publication #%1$s for task %2$s', 'staatic'),
                $publicationId,
                $taskName
            ));

            return \false;
        }
        if ($taskName !== $publication->currentTask()) {
            $publication->setCurrentTask($taskName);
            $this->publicationRepository->update($publication);
            $this->logger->info($task->description());
            \do_action('staatic_publication_before_task', [
                'publication' => $publication,
                'task' => $task
            ]);
        }

        try {
            $taskFinished = $task->execute($publication);
            $this->updatePublicationUnlessCanceled($publication);
        } catch (Error $error) {
            $publication->markFailed();
            $this->publicationRepository->update($publication);
            \update_option('staatic_current_publication_id', '');
            $this->logger->critical(\sprintf(
                /* translators: 1: Publication task, 2: Error message. */
                \__('PHP error during task %1$s: %2$s', 'staatic'),
                $taskName,
                $this->debug ? (string) $error : $error->getMessage()
            ));

            return \false;
        } catch (Exception $exception) {
            $publication->markFailed();
            $this->publicationRepository->update($publication);
            \update_option('staatic_current_publication_id', '');
            $this->logger->error(\sprintf(
                /* translators: 1: Publication task, 2: Exception message. */
                \__('Error during task %1$s: %2$s', 'staatic'),
                $taskName,
                $this->debug ? (string) $exception : $exception->getMessage()
            ));

            return \false;
        }
        // If the task has not finished, restart task.
        if ($taskFinished === \false) {
            return $taskName;
        }
        \do_action('staatic_publication_after_task', [
            'publication' => $publication,
            'task' => $task
        ]);
        // Otherwise find the next task.
        $nextTask = $publication->status()->isInProgress() ? $this->publicationTaskProvider->nextSupportedTask(
            $task,
            $publication
        ) : null;
        // Continue with next task or quit.
        if ($nextTask) {
            return \get_class($nextTask);
        }
        $this->logger->notice(\__('Finished publication', 'staatic'), [
            'publicationId' => $publication->id()
        ]);

        return \false;
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete()
    {
        parent::complete();
        \update_option('staatic_current_publication_id', '');
    }

    /**
     * @param Publication $publication
     * @return void
     */
    protected function updatePublicationUnlessCanceled($publication)
    {
        if ($this->isPublicationCanceled($publication)) {
            return;
        }
        $this->publicationRepository->update($publication);
    }

    /**
     * @param Publication $publication
     */
    protected function isPublicationCanceled($publication) : bool
    {
        $freshPublication = $this->publicationRepository->find($publication->id());

        return $freshPublication->status()->isCanceled();
    }
}
