<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Admin\Page;

use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Service\Diagnostics;

final class DiagnosticsPage implements ModuleInterface
{
    /**
     * @var Diagnostics
     */
    private $diagnostics;

    public function __construct(Diagnostics $diagnostics)
    {
        $this->diagnostics = $diagnostics;
    }

    /**
     * @return void
     */
    public function hooks()
    {
        if (!\is_admin()) {
            return;
        }
        \add_action('wp_loaded', [$this, 'handle']);
    }

    /**
     * @return void
     */
    public function handle()
    {
        if (!isset($_REQUEST['staatic']) || $_REQUEST['staatic'] !== 'diagnostics') {
            return;
        }
        $json = \json_encode($this->diagnostics->retrieve(), \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
        \header('Content-Disposition: attachment; filename="staatic-diagnostics.json"');
        \header('Content-Type: application/json');
        \header(\sprintf('Content-Length: %d', \strlen($json)));
        echo $json;
        die;
    }
}
