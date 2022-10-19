<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\Publications;

use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Service\AdminNavigation;

final class PublicationDeletePage implements ModuleInterface
{
    /** @var string */
    const PAGE_SLUG = 'staatic-publication-delete';

    /**
     * @var AdminNavigation
     */
    private $navigation;

    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    public function __construct(AdminNavigation $navigation, PublicationRepository $publicationRepository)
    {
        $this->navigation = $navigation;
        $this->publicationRepository = $publicationRepository;
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
            \__('Delete Publication', 'staatic'),
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
        \check_admin_referer('staatic-publication-delete_' . $publicationId);
        if (!($publication = $this->publicationRepository->find($publicationId))) {
            \wp_die(\__('Invalid publication.', 'staatic'));
        }
        if (\in_array(
            $publication->id(),
            [
                \get_option('staatic_current_publication_id'),
                \get_option('staatic_latest_publication_id'),
                \get_option('staatic_active_publication_id'),
                \get_option('staatic_active_preview_publication_id')
            ],
            \true
        )) {
            \wp_die(\__('Unable to delete this publication.', 'staatic'));
        }
        $this->publicationRepository->delete($publication);
        $redirectTo = \remove_query_arg(['deleted', 'item'], \wp_get_referer());
        $redirectTo = \add_query_arg('deleted', 1, $redirectTo);
        \wp_safe_redirect($redirectTo);
        exit;
    }

    /**
     * @return void
     */
    public function render()
    {
    }
}
