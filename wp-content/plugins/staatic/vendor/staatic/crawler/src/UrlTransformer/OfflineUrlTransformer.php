<?php

namespace Staatic\Crawler\UrlTransformer;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\GuzzleHttp\Psr7\UriResolver;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class OfflineUrlTransformer implements UrlTransformerInterface
{
    /**
     * @param UriInterface $url
     * @param UriInterface|null $foundOnUrl
     */
    public function transform($url, $foundOnUrl = null) : UrlTransformation
    {
        if (!$url->getPath()) {
            $url = $url->withPath('/');
        }
        $transformedUrl = $url->withScheme('')->withUserInfo('')->withHost('')->withPort(null);
        $effectiveUrl = $foundOnUrl ? UriResolver::relativize($foundOnUrl, $url) : new Uri('');
        $effectiveUrl = $this->addIndexIfNeeded($effectiveUrl);
        return new UrlTransformation($transformedUrl, $effectiveUrl);
    }
    private function addIndexIfNeeded(UriInterface $url) : UriInterface
    {
        $path = $url->getPath();
        if ($path === '') {
            return $url;
        }
        if (substr_compare($path, '/', -strlen('/')) === 0) {
            return $url->withPath($path . 'index.html');
        }
        if (strpos(\basename($path), '.') === false) {
            return $url->withPath(\rtrim($path, '/') . '/index.html');
        }
        return $url;
    }
}
