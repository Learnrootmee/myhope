<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

final class RegisterOptions implements ModuleInterface
{
    /**
     * @return void
     */
    public function hooks()
    {
        \add_option('staatic_current_publication_id');
        \add_option('staatic_latest_publication_id');
        \add_option('staatic_active_publication_id');
        \add_option('staatic_active_preview_publication_id');
    }

    public static function getDefaultPriority() : int
    {
        return 40;
    }
}
