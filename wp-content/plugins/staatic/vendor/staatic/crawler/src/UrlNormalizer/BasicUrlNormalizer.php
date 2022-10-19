<?php

namespace Staatic\Crawler\UrlNormalizer;

use Staatic\Vendor\GuzzleHttp\Psr7\UriNormalizer;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class BasicUrlNormalizer implements UrlNormalizerInterface
{
    /**
     * @param UriInterface $url
     */
    public function normalize($url) : UriInterface
    {
        if ($url->getFragment()) {
            $url = $url->withFragment('');
        }
        $flags = UriNormalizer::PRESERVING_NORMALIZATIONS | UriNormalizer::REMOVE_DUPLICATE_SLASHES | UriNormalizer::SORT_QUERY_PARAMETERS;
        return UriNormalizer::normalize($url, $flags);
    }
}
