<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

use Staatic\WordPress\Publication\PublicationManager;

final class RegisterPublishHook implements ModuleInterface
{
    /**
     * @var PublicationManager
     */
    private $publicationManager;

    /**
     * @var string
     */
    private $publishHook;

    public function __construct(PublicationManager $publicationManager, string $publishHook)
    {
        $this->publicationManager = $publicationManager;
        $this->publishHook = $publishHook;
    }

    /**
     * @return void
     */
    public function hooks()
    {
        \add_action($this->publishHook, [$this, 'publish']);
    }

    /**
     * @return void
     */
    public function publish()
    {
        if ($this->publicationManager->isPublicationInProgress()) {
            return;
        }
        $publication = $this->publicationManager->createPublication();
        if ($this->publicationManager->claimPublication($publication)) {
            $this->publicationManager->initiateBackgroundPublisher($publication);
        } else {
            $this->publicationManager->cancelPublication($publication);
        }
    }
}
