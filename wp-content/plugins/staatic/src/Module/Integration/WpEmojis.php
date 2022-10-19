<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Integration;

use Staatic\WordPress\Module\ModuleInterface;

final class WpEmojis implements ModuleInterface
{
    /**
     * @var mixed[]
     */
    private $emojiFiles = [];

    /**
     * @return void
     */
    public function hooks()
    {
        \add_action('wp_loaded', [$this, 'setupIntegration']);
    }

    /**
     * @return void
     */
    public function setupIntegration()
    {
        $this->emojiFiles = $this->detectEmojiFiles();
        if (empty($this->emojiFiles)) {
            return;
        }
        \add_filter('staatic_additional_paths', [$this, 'overrideAdditionalPaths']);
    }

    private function detectEmojiFiles() : array
    {
        $candidatePaths = [
            \ABSPATH . \WPINC . '/js/wp-emoji-release.min.js',
            \ABSPATH . \WPINC . '/js/wp-emoji.js',
            \ABSPATH . \WPINC . '/js/twemoji.js'
        ];

        return \array_filter($candidatePaths, function ($path) {
            return \file_exists($path);
        });
    }

    /**
     * @param mixed[] $additionalPaths
     */
    public function overrideAdditionalPaths($additionalPaths) : array
    {
        $extraAdditionalPaths = [];
        foreach ($this->emojiFiles as $path) {
            $extraAdditionalPaths[$path] = [
                'path' => $path,
                'dontTouch' => \false,
                'dontFollow' => \false,
                'dontSave' => \false
            ];
        }

        return \array_merge($extraAdditionalPaths, $additionalPaths);
    }
}
