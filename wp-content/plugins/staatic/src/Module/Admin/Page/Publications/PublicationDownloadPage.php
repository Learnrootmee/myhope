<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\Publications;

use Exception;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Service\PublicationArchiver;

final class PublicationDownloadPage implements ModuleInterface
{
    /** @var string */
    const PAGE_SLUG = 'staatic-publication-download';

    /**
     * @var AdminNavigation
     */
    private $navigation;

    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var PublicationArchiver
     */
    private $publicationArchiver;

    public function __construct(
        AdminNavigation $navigation,
        PublicationRepository $publicationRepository,
        PublicationArchiver $publicationArchiver
    )
    {
        $this->navigation = $navigation;
        $this->publicationRepository = $publicationRepository;
        $this->publicationArchiver = $publicationArchiver;
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!\is_admin()) {
            return;
        }
        \add_action('init', [$this, 'addPage']);
    }

    /**
     * @return void
     */
    public function addPage()
    {
        $this->navigation->addPage(
            \__('Download Publication', 'staatic'),
            self::PAGE_SLUG,
            [$this, 'render'],
            'edit_posts',
            PublicationsPage::PAGE_SLUG,
            [$this, 'load']
        );
    }

    /**
     * @return void
     */
    public function load()
    {
        $publicationId = isset($_REQUEST['id']) ? \sanitize_key($_REQUEST['id']) : null;
        if (!$publicationId) {
            \wp_die(\__('Missing publication id.', 'staatic'));
        }
        if (!$this->publicationRepository->find($publicationId)) {
            \wp_die(\__('Invalid publication.', 'staatic'));
        }

        try {
            $this->publicationArchiver->archive($publicationId);
        } catch (Exception $e) {
            \wp_die(\sprintf(
                /* translators: %s: Error Message. */
                \__('Unable to generate archive: %s.', 'staatic'),
                $e->getMessage()
            ));
        }
    }

    /**
     * @return void
     */
    public function render()
    {
    }
}
