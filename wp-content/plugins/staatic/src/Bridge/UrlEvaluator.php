<?php

namespace Staatic\WordPress\Bridge;

use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Crawler\UrlEvaluator\UrlEvaluatorInterface;
use Staatic\Crawler\UrlEvaluator\WordPressUrlEvaluator;

final class UrlEvaluator implements UrlEvaluatorInterface
{
    /**
     * @var UrlEvaluatorInterface
     */
    private $decoratedEvaluator;

    /**
     * @var mixed[]
     */
    private $simpleExcludeRules = [];

    /**
     * @var mixed[]
     */
    private $wildcardExcludeRules = [];

    /**
     * @var UriInterface
     */
    private $baseUrl;

    public function __construct(UriInterface $baseUrl, array $excludeUrls = [])
    {
        $this->baseUrl = $baseUrl;
        $this->decoratedEvaluator = new WordPressUrlEvaluator($baseUrl);
        $this->initializeExcludeRules($excludeUrls);
    }

    /**
     * @return void
     */
    private function initializeExcludeRules(array $excludeUrls)
    {
        foreach ($excludeUrls as $excludeUrl) {
            if (\strstr($excludeUrl, '*') === \false) {
                $this->simpleExcludeRules[] = \mb_strtolower($excludeUrl);
            } else {
                $this->wildcardExcludeRules[] = \sprintf(
                    '~^%s$~i',
                    \str_replace('\\*', '.+?', \preg_quote($excludeUrl, '~'))
                );
            }
        }
    }

    /**
     * @param UriInterface $resolvedUrl
     */
    public function shouldCrawl($resolvedUrl) : bool
    {
        $withoutHost = (string) $resolvedUrl->withScheme('')->withHost('')->withPort(null);
        // Simple exclude rules.
        foreach ($this->simpleExcludeRules as $rule) {
            if (\strcasecmp($withoutHost, $rule) === 0) {
                return \false;
            }
        }
        // Wildcard exclude rules.
        foreach ($this->wildcardExcludeRules as $rule) {
            if (\preg_match($rule, $withoutHost) === 1) {
                return \false;
            }
        }

        return $this->decoratedEvaluator->shouldCrawl($resolvedUrl);
    }
}
