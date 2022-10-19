<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module;

final class LoadTextDomain implements ModuleInterface
{
    /**
     * @return void
     */
    public function hooks()
    {
        \add_action('init', [$this, 'loadTextDomain'], 0);
    }

    /**
     * @return void
     */
    public function loadTextDomain()
    {
        \load_plugin_textdomain('staatic', \false, \dirname(\plugin_basename(\STAATIC_FILE)) . '/languages/');
    }

    public static function getDefaultPriority() : int
    {
        return 80;
    }
}
