<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page\Publications;

use Staatic\WordPress\Bridge\ResultRepository;
use Staatic\WordPress\Logging\LogEntryRepository;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Publication\Publication;
use Staatic\WordPress\Publication\PublicationRepository;
use Staatic\WordPress\Service\AdminNavigation;
use Staatic\WordPress\Service\PartialRenderer;

final class PublicationSummaryPage implements ModuleInterface
{
    /** @var string */
    const PAGE_SLUG = 'staatic-publication';

    /**
     * @var Publication|null
     */
    private $publication;

    /**
     * @var AdminNavigation
     */
    private $navigation;

    /**
     * @var PartialRenderer
     */
    private $renderer;

    /**
     * @var PublicationRepository
     */
    private $publicationRepository;

    /**
     * @var ResultRepository
     */
    private $resultRepository;

    /**
     * @var LogEntryRepository
     */
    private $logEntryRepository;

    public function __construct(
        AdminNavigation $navigation,
        PartialRenderer $renderer,
        PublicationRepository $publicationRepository,
        ResultRepository $resultRepository,
        LogEntryRepository $logEntryRepository
    )
    {
        $this->navigation = $navigation;
        $this->renderer = $renderer;
        $this->publicationRepository = $publicationRepository;
        $this->resultRepository = $resultRepository;
        $this->logEntryRepository = $logEntryRepository;
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!\is_admin()) {
            return;
        }
        $this->navigation->addPage(
            \__('Publication Summary', 'staatic'),
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
        if (!($this->publication = $this->publicationRepository->find($publicationId))) {
            \wp_die(\__('Invalid publication.', 'staatic'));
        }
    }

    /**
     * @return void
     */
    public function render()
    {
        $logEntries = $this->logEntryRepository->findWhereMatching(
            $this->publication->id(),
            ['notice', 'warning', 'error', 'critical'],
            null,
            50,
            0,
            'log_date',
            'ASC'
        );
        $resultsPerStatusCategory = $this->resultRepository->getResultsPerStatusCategory(
            $this->publication->build()->id()
        );
        $this->renderer->render('admin/publication/summary.php', [
            'publication' => $this->publication,
            'logEntries' => $logEntries,
            'resultsPerStatusCategory' => $resultsPerStatusCategory
        ]);
    }
}
