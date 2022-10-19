<?php

namespace Staatic\Crawler\UrlNormalizer;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class WordPressUrlNormalizer implements UrlNormalizerInterface
{
    /**
     * @var UrlNormalizerInterface
     */
    private $decoratedNormalizer;
    public function __construct()
    {
        $this->decoratedNormalizer = new InternalUrlNormalizer();
    }
    /**
     * @param UriInterface $url
     */
    public function normalize($url) : UriInterface
    {
        if ($url->getQuery()) {
            $url = $url->withQuery('');
        }
        return $this->decoratedNormalizer->normalize($url);
    }
}
