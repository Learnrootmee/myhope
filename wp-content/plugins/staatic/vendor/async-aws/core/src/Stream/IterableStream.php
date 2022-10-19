<?php

namespace Staatic\Vendor\AsyncAws\Core\Stream;

use Traversable;
use Staatic\Vendor\AsyncAws\Core\Exception\InvalidArgument;
final class IterableStream implements ReadOnceResultStream, RequestStream
{
    private $content;
    /**
     * @param mixed[] $content
     */
    private function __construct($content)
    {
        $this->content = $content;
    }
    public static function create($content) : IterableStream
    {
        if ($content instanceof self) {
            return $content;
        }
        if (is_array($content) || $content instanceof Traversable) {
            return new self($content);
        }
        throw new InvalidArgument(\sprintf('Expect content to be an iterable. "%s" given.', \is_object($content) ? \get_class($content) : \gettype($content)));
    }
    /**
     * @return int|null
     */
    public function length()
    {
        return null;
    }
    public function stringify() : string
    {
        if ($this->content instanceof Traversable) {
            return \implode('', \iterator_to_array($this->content));
        }
        return \implode('', \iterator_to_array((function () {
            yield from $this->content;
        })()));
    }
    public function getIterator() : Traversable
    {
        yield from $this->content;
    }
    /**
     * @param string $algo
     * @param bool $raw
     */
    public function hash($algo = 'sha256', $raw = \false) : string
    {
        $ctx = \hash_init($algo);
        foreach ($this->content as $chunk) {
            \hash_update($ctx, $chunk);
        }
        return \hash_final($ctx, $raw);
    }
}
