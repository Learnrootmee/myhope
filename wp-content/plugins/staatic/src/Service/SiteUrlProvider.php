<?php

declare(strict_types=1);

namespace Staatic\WordPress\Service;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\Psr\Http\Message\UriInterface;

final class SiteUrlProvider
{
    public function provide() : UriInterface
    {
        $siteUrl = $_ENV['STAATIC_SITE_URL'] ?? \site_url();
        $siteUrl = \apply_filters('staatic_site_url', $siteUrl);
        $siteUrl = new Uri($siteUrl);
        // Make sure that the site URL ends with a slash to prevent unnecessary
        // redirect (WordPress usually redirects "/page" to "/page/").
        if (!$siteUrl->getPath()) {
            $siteUrl = $siteUrl->withPath('/');
        } elseif (substr_compare($siteUrl->getPath(), '/', -strlen('/')) !== 0) {
            $siteUrl = $siteUrl->withPath($siteUrl->getPath() . '/');
        }

        return $siteUrl;
    }
}
