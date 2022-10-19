<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\Publications;

use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Service\PartialRenderer;

final class PublicationsPage implements ModuleInterface
{
    /** @var string */
    const PAGE_SLUG = 'staatic-publications';

    /**
     * @var AdminNavigation
     */
    private $navigation;

    /**
     * @var PartialRenderer
     */
    private $renderer;

    /**
     * @var PublicationsTable
     */
    private $listTable;

    public function __construct(AdminNavigation $navigation, PartialRenderer $renderer, PublicationsTable $listTable)
    {
        $this->navigation = $navigation;
        $this->renderer = $renderer;
        $this->listTable = $listTable;
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!\is_admin()) {
            return;
        }
        \add_action('init', [$this, 'addMenuItem']);
        $this->listTable->registerHooks(\sprintf('staatic_page_%s', self::PAGE_SLUG));
    }

    /**
     * @return void
     */
    public function addMenuItem()
    {
        $this->navigation->addMenuItem(
            \__('Publications', 'staatic'),
            \__('Latest Publications', 'staatic'),
            self::PAGE_SLUG,
            [$this, 'render'],
            'edit_posts',
            [$this, 'load'],
            10
        );
    }

    /**
     * @return void
     */
    public function load()
    {
        // Display notices.
        $deleted = isset($_REQUEST['deleted']) ? (int) $_REQUEST['deleted'] : 0;
        $messages = [];
        if ($deleted > 0) {
            /* translators: %s: Number of publications. */
            $messages[] = \sprintf(
                \_n('%s publication deleted.', '%s publications deleted.', $deleted),
                \number_format_i18n($deleted)
            );
        }
        if (\count($messages) > 0) {
            \add_action('admin_notices', function () use ($messages) {
                \printf('<div class="updated notice is-dismissible"><p>%s</p></div>', \implode("<br>\n", $messages));
            });
        }
        // Setup list table.
        $this->listTable->initialize(\admin_url(\sprintf('admin.php?page=%s', self::PAGE_SLUG)));
        $this->listTable->processBulkActions();
    }

    /**
     * @return void
     */
    public function render()
    {
        $listTable = $this->listTable->wpListTable();
        $listTable->prepare_items();
        $this->renderer->render('admin/publication/list.php', \compact('listTable'));
    }
}
