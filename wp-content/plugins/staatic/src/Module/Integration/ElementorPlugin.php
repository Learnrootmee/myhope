<?php

declare(strict_types=1);

namespace Staatic\WordPress\Module\Integration;

use Staatic\Vendor\GuzzleHttp\Psr7\Uri;
use Staatic\Vendor\GuzzleHttp\Psr7\Utils;
use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use Staatic\Crawler\UrlTransformer\OfflineUrlTransformer;
use Staatic\Framework\Resource;
use Staatic\Framework\Result;
use Staatic\Framework\Transformer\TransformerInterface;
use Staatic\WordPress\Factory\UrlTransformerFactory;
use Staatic\WordPress\Module\ModuleInterface;
use Staatic\WordPress\Setting\Build\DestinationUrlSetting;

final class ElementorPlugin implements ModuleInterface
{
    const READ_MAXIMUM_BYTES = 1024 * 1024 * 16;

    /**
     * @var UrlTransformerFactory
     */
    private $urlTransformerFactory;

    /**
     * @var DestinationUrlSetting
     */
    private $destinationUrl;

    // 16MB
    public function __construct(UrlTransformerFactory $urlTransformerFactory, DestinationUrlSetting $destinationUrl)
    {
        $this->urlTransformerFactory = $urlTransformerFactory;
        $this->destinationUrl = $destinationUrl;
    }

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
        if (!$this->isPluginActive()) {
            return;
        }
        $transformer = ($this->urlTransformerFactory)(new Uri($this->destinationUrl->value()));
        if (!$transformer instanceof OfflineUrlTransformer) {
            return;
        }
        \add_filter('staatic_transformers', [$this, 'registerElementorTransformer']);
    }

    /**
     * @param mixed[] $transformers
     */
    public function registerElementorTransformer($transformers) : array
    {
        // Offline URL transformer causes the assets URL within Elementor[Pro]FrontendConfig
        // to be malformed. This transformer corrects this.
        $transformers[] = new class(self::READ_MAXIMUM_BYTES) implements TransformerInterface, LoggerAwareInterface {
            use LoggerAwareTrait;

            /**
             * @var int
             */
            private $readMaximumBytes;

            public function __construct(int $readMaximumBytes)
            {
                $this->readMaximumBytes = $readMaximumBytes;
                $this->logger = new NullLogger();
            }

            /**
             * @param Result $result
             */
            public function supports($result) : bool
            {
                return $result->mimeType() === 'text/html' && $result->size() > 0;
            }

            /**
             * @param Result $result
             * @param Resource $resource
             * @return void
             */
            public function transform($result, $resource)
            {
                $this->logger->info("Applying Elementor transformer on '{$result->url()}'");
                $content = $resource->content()->read($this->readMaximumBytes);
                $resource->content()->rewind();
                $content = \preg_replace_callback(
                    '~"assets":"[^"]+?\\\\/wp-content\\\\/plugins\\\\/elementor(?:-pro)?\\\\/assets\\\\/index\\.html"~',
                    function ($match) {
                    return \str_replace('assets\\/index.html', 'assets\\/', $match[0]);
                },
                    $content,
                    -1,
                    $count
                );
                if (!$count) {
                    $this->logger->debug("Elementor config not found on '{$result->url()}'");

                    return;
                }
                $this->logger->debug("Elementor config corrected on '{$result->url()}: {$count} replacements'");
                $resource->replace(Utils::streamFor($content));
                $result->syncResource($resource);
            }
        };

        return $transformers;
    }

    private function isPluginActive() : bool
    {
        return \defined('ELEMENTOR_VERSION');
    }
}
