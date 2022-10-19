<?php

namespace Staatic\Crawler\UrlExtractor;

use Closure;
use Generator;
use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\GuzzleHttp\Psr7\UriResolver;
use InvalidArgumentException;
use Staatic\Vendor\Psr\Http\Message\UriInterface;
use Staatic\Vendor\voku\helper\DomParserInterface;
use Staatic\Crawler\UriHelper;
use Staatic\Crawler\UrlExtractor\Mapping\HtmlUrlExtractorMapping;
use Staatic\Crawler\UrlTransformer\UrlTransformation;
use Staatic\Vendor\voku\helper\HtmlDomParser;
use Staatic\Vendor\voku\helper\SimpleHtmlDom;
final class HtmlUrlExtractor implements UrlExtractorInterface, FilterableInterface, TransformableInterface
{
    /**
     * @var DomParserInterface
     */
    private $domParser;
    /**
     * @var UrlExtractorInterface
     */
    private $cssExtractor;
    /**
     * @var HtmlUrlExtractorMapping
     */
    private $mapping;
    /**
     * @var \Closure|null
     */
    private $filterCallback;
    /**
     * @var \Closure|null
     */
    private $transformCallback;
    /**
     * @param DomParserInterface|null $domParser
     * @param UrlExtractorInterface|null $cssExtractor
     * @param HtmlUrlExtractorMapping|null $mapping
     */
    public function __construct($domParser = null, $cssExtractor = null, $mapping = null)
    {
        $this->domParser = $domParser ?? new HtmlDomParser();
        $this->cssExtractor = $cssExtractor ?? new CssUrlExtractor();
        $this->mapping = $mapping ?? new HtmlUrlExtractorMapping();
    }
    /**
     * @param string $content
     * @param UriInterface $baseUrl
     */
    public function extract($content, $baseUrl) : Generator
    {
        $domParser = clone $this->domParser;
        $domParser->loadHtml($content);
        foreach ($this->mapping->mapping() as $tagName => $attributes) {
            foreach ($domParser->find($tagName) as $element) {
                yield from $this->handleElementAttributes($element, $attributes, $baseUrl);
            }
        }
        foreach ($domParser->find('style') as $element) {
            $elementTextContent = $this->decodeHtmlEntities($element->textContent);
            $elementTextContentBefore = $elementTextContent;
            $generator = $this->cssExtractor->extract($element->textContent, $baseUrl);
            yield from $generator;
            $elementTextContent = $generator->getReturn();
            if ($elementTextContent !== $elementTextContentBefore) {
                $element->textContent = $this->encodeSpecialChars($elementTextContent);
            }
        }
        $newContent = $domParser->html();
        if (empty($newContent)) {
        }
        return $newContent ?: $content;
    }
    private function handleElementAttributes(SimpleHtmlDom $element, array $attributes, UriInterface $baseUrl) : Generator
    {
        if ($element->hasAttribute('style')) {
            $attributeValue = $this->decodeHtmlEntities($element->getAttribute('style'));
            $attributeValueBefore = $attributeValue;
            $generator = $this->cssExtractor->extract($attributeValue, $baseUrl);
            yield from $generator;
            $attributeValue = $generator->getReturn();
            if ($attributeValue !== $attributeValueBefore) {
                $element->setAttribute('style', $this->encodeSpecialChars($attributeValue));
            }
        }
        foreach ($attributes as $attributeName) {
            if (!$element->hasAttribute($attributeName)) {
                continue;
            }
            $attributeValue = $this->decodeHtmlEntities($element->getAttribute($attributeName));
            $attributeValueBefore = $attributeValue;
            if (\in_array($attributeName, $this->mapping->srcsetAttributes())) {
                $extractedUrls = $this->extractUrlsFromSrcset($attributeValue);
            } else {
                $extractedUrls = [$attributeValue];
            }
            foreach ($extractedUrls as $extractedUrl) {
                $extractedUrl = \trim($extractedUrl);
                if (!UriHelper::isReplaceableUrl($extractedUrl)) {
                    continue;
                }
                $preserveEmptyFragment = substr_compare($extractedUrl, '#', -strlen('#')) === 0;
                try {
                    $resolvedUrl = UriResolver::resolve($baseUrl, new Uri($extractedUrl));
                } catch (InvalidArgumentException $e) {
                    continue;
                }
                if ($this->filterCallback && ($this->filterCallback)($resolvedUrl)) {
                    $attributeValue = \str_replace($extractedUrl, (string) $resolvedUrl . ($preserveEmptyFragment ? '#' : ''), $attributeValue);
                    continue;
                }
                $urlTransformation = $this->transformCallback ? ($this->transformCallback)($resolvedUrl, $baseUrl) : new UrlTransformation($resolvedUrl);
                (yield (string) $resolvedUrl => $urlTransformation->transformedUrl());
                $attributeValue = \str_replace($extractedUrl, (string) $urlTransformation->effectiveUrl() . ($preserveEmptyFragment ? '#' : ''), $attributeValue);
            }
            if ($attributeValue !== $attributeValueBefore) {
                $element->setAttribute($attributeName, $this->encodeSpecialChars($attributeValue));
            }
        }
    }
    private function decodeHtmlEntities(string $input) : string
    {
        return \html_entity_decode($input, \ENT_QUOTES | \ENT_SUBSTITUTE | \ENT_HTML5, 'UTF-8');
    }
    private function encodeSpecialChars(string $input) : string
    {
        return \htmlspecialchars($input, \ENT_QUOTES | \ENT_SUBSTITUTE | \ENT_HTML5, 'UTF-8');
    }
    private function extractUrlsFromSrcset(string $srcset) : array
    {
        \preg_match_all('~([^\\s]+)\\s*(?:[\\d\\.]+[wx])?,*~m', $srcset, $matches);
        return $matches[1];
    }
    /**
     * @param callable|null $callback
     * @return void
     */
    public function setFilterCallback($callback)
    {
        $callable = $callback;
        $this->filterCallback = $callback ? function () use ($callable) {
            return $callable(...func_get_args());
        } : null;
        if ($this->cssExtractor instanceof FilterableInterface) {
            $this->cssExtractor->setFilterCallback($callback);
        }
    }
    /**
     * @param callable|null $callback
     * @return void
     */
    public function setTransformCallback($callback)
    {
        $callable = $callback;
        $this->transformCallback = $callback ? function () use ($callable) {
            return $callable(...func_get_args());
        } : null;
        if ($this->cssExtractor instanceof TransformableInterface) {
            $this->cssExtractor->setTransformCallback($callback);
        }
    }
}
