<?php

namespace Staatic\Framework\Transformer;

use Staatic\Vendor\Psr\Log\LoggerAwareInterface;
use Staatic\Vendor\Psr\Log\LoggerAwareTrait;
use Staatic\Vendor\Psr\Log\NullLogger;
use RuntimeException;
use Staatic\Framework\Resource;
use Staatic\Framework\Result;
final class StaaticTransformer implements TransformerInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    const GENERATOR_STRING = "<!-- Powered by Staatic (https://staatic.com/) -->";
    public function __construct()
    {
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
        $this->logger->info("Applying Staatic transformer on '{$result->url()}'");
        $content = $resource->content();
        $length = \strlen(self::GENERATOR_STRING);
        $found = \false;
        try {
            $content->seek($length * -1, \SEEK_END);
            if ($content->read($length) === self::GENERATOR_STRING) {
                $found = \true;
            }
        } catch (RuntimeException $e) {
        }
        $content->rewind();
        if ($found) {
            return;
        }
        $content->seek(0, \SEEK_END);
        $content->write(self::GENERATOR_STRING);
        $content->rewind();
        $resource->replace($content);
        $result->syncResource($resource);
    }
}
