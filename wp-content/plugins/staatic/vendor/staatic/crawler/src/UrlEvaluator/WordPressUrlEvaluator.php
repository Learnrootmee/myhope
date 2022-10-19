<?php

namespace Staatic\Crawler\UrlEvaluator;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
final class WordPressUrlEvaluator implements UrlEvaluatorInterface
{
    /**
     * @var UrlEvaluatorInterface
     */
    private $decoratedEvaluator;
    /**
     * @var UriInterface
     */
    private $baseUrl;
    public function __construct(UriInterface $baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->decoratedEvaluator = new InternalUrlEvaluator($baseUrl);
    }
    /**
     * @param UriInterface $resolvedUrl
     */
    public function shouldCrawl($resolvedUrl) : bool
    {
        $basePath = $this->baseUrl->getPath();
        $path = $resolvedUrl->getPath();
        if ($basePath && $basePath !== '/') {
            if (strncmp($path, $basePath, strlen($basePath)) !== 0) {
                return \false;
            }
            $path = \mb_substr($path, \mb_strlen(\rtrim($basePath, '/')));
        }
        if (strncmp($path, '/xmlrpc.php', strlen('/xmlrpc.php')) === 0) {
            return \false;
        }
        if (strncmp($path, '/wp-comments-post.php', strlen('/wp-comments-post.php')) === 0) {
            return \false;
        }
        if (strncmp($path, '/wp-login.php', strlen('/wp-login.php')) === 0) {
            return \false;
        }
        if (\rtrim($path, '/') === '/wp-admin') {
            return \false;
        }
        $withoutHost = $resolvedUrl->withScheme('')->withHost('')->withPort(null)->withUserInfo('');
        if (\preg_match('~^/\\?p=\\d+~', (string) $withoutHost)) {
            return \false;
        }
        return $this->decoratedEvaluator->shouldCrawl($resolvedUrl);
    }
}
