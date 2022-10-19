<?php

namespace Staatic\Framework\Transformer;

use ArrayIterator;
use IteratorAggregate;
use RuntimeException;
use Staatic\Framework\Resource;
use Staatic\Framework\Result;
use Traversable;
final class TransformerCollection implements IteratorAggregate
{
    /**
     * @var mixed[]
     */
    private $transformers = [];
    public function __construct(array $transformers = [])
    {
        $this->addTransformers($transformers);
    }
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->transformers);
    }
    /**
     * @param mixed[] $transformers
     * @return void
     */
    public function addTransformers($transformers)
    {
        foreach ($transformers as $transformer) {
            $this->addTransformer($transformer);
        }
    }
    /**
     * @param TransformerInterface $transformer
     * @return void
     */
    public function addTransformer($transformer)
    {
        $this->transformers[] = $transformer;
    }
    /**
     * @param Result $result
     * @param Resource $resource
     * @return void
     */
    public function apply($result, $resource)
    {
        foreach ($this->transformers as $transformer) {
            if (!$transformer->supports($result, $resource)) {
                continue;
            }
            $transformer->transform($result, $resource);
            if ($resource->content()->tell() !== 0) {
                throw new RuntimeException(\sprintf('Resource content stream was not left in a valid state since "%s"', \get_class($transformer)));
            }
        }
    }
}
