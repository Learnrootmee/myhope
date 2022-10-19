<?php

declare(strict_types=1);

namespace Staatic\WordPress\Setting\Build;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\WordPress\Service\SiteUrlProvider;
use Staatic\WordPress\Setting\AbstractSetting;

final class ExcludeUrlsSetting extends AbstractSetting
{
    /**
     * @var SiteUrlProvider
     */
    private $siteUrlProvider;

    public function __construct(SiteUrlProvider $siteUrlProvider)
    {
        $this->siteUrlProvider = $siteUrlProvider;
    }

    public function name() : string
    {
        return 'staatic_exclude_urls';
    }

    public function type() : string
    {
        return self::TYPE_STRING;
    }

    public function label() : string
    {
        return \__('Excluded URLs', 'staatic');
    }

    /**
     * @return string|null
     */
    public function description()
    {
        return \__('Optionally add URLs that need to be excluded in the build.', 'staatic');
    }

    public function sanitizeValue($value)
    {
        $siteUrl = $this->siteUrlProvider->provide();
        $excludeUrls = [];
        foreach (\explode("\n", $value) as $excludeUrl) {
            $excludeUrl = \trim($excludeUrl);
            if (!$excludeUrl || strncmp($excludeUrl, '#', strlen('#')) === 0) {
                $excludeUrls[] = $excludeUrl;

                continue;
            }
            $authority = (new Uri($excludeUrl))->getAuthority();
            if ($authority && $authority !== $siteUrl->getAuthority()) {
                \add_settings_error('staatic-settings', 'invalid_exclude_url', \sprintf(
                    /* translators: %s: Supplied excluded URL. */
                    \__('The supplied excluded URL "%s" is not part of this site and therefore skipped', 'staatic'),
                    $excludeUrl
                ));
                $excludeUrls[] = \sprintf('#%s', $excludeUrl);

                continue;
            }
            if (!\in_array($excludeUrl, $excludeUrls)) {
                $excludeUrls[] = $excludeUrl;
            }
        }

        return \implode("\n", $excludeUrls);
    }

    /**
     * @param string|null $value
     */
    public static function resolvedValue($value)
    {
        $resolvedValue = [];
        if ($value === null) {
            return $resolvedValue;
        }
        foreach (\explode("\n", $value) as $excludeUrl) {
            if (!$excludeUrl || strncmp($excludeUrl, '#', strlen('#')) === 0) {
                continue;
            }
            $resolvedValue[] = $excludeUrl;
        }

        return $resolvedValue;
    }

    /**
     * @param mixed[] $attributes
     * @return void
     */
    public function render($attributes = [])
    {
        $this->renderer->render('admin/settings/excluded_urls.php', [
            'setting' => $this,
            'attributes' => $attributes
        ]);
    }
}
