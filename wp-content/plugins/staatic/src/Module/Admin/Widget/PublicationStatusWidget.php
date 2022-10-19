<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Widget;

use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Service\PartialRenderer;

final class PublicationStatusWidget implements ModuleInterface
{
    /**
     * @var PartialRenderer
     */
    private $renderer;

    public function __construct(PartialRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!\is_admin()) {
            return;
        }
        \add_action('wp_dashboard_setup', [$this, 'addDashboardWidget']);
    }

    /**
     * @return void
     */
    public function addDashboardWidget()
    {
        \wp_add_dashboard_widget(
            'staatic_publication_status_widget',
            \__('Staatic Publication Status', 'staatic'),
            [$this, 'render']
        );
    }

    /**
     * @return void
     */
    public function render()
    {
        $publicationId = \get_option('staatic_latest_publication_id');
        $this->renderer->render('admin/widgets/publication-status.php', \compact('publicationId'));
    }
}
