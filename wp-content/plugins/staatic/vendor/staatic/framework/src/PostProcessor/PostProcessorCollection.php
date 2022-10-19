<?php

namespace Staatic\Framework\PostProcessor;

use ArrayIterator;
use IteratorAggregate;
use Staatic\Framework\Build;
use Traversable;
final class PostProcessorCollection implements IteratorAggregate
{
    /**
     * @var mixed[]
     */
    private $postProcessors = [];
    public function __construct(array $postProcessors = [])
    {
        $this->addPostProcessors($postProcessors);
    }
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->postProcessors);
    }
    /**
     * @param mixed[] $postProcessors
     * @return void
     */
    public function addPostProcessors($postProcessors)
    {
        foreach ($postProcessors as $postProcessor) {
            $this->addPostProcessor($postProcessor);
        }
    }
    /**
     * @param PostProcessorInterface $postProcessor
     * @return void
     */
    public function addPostProcessor($postProcessor)
    {
        $this->postProcessors[] = $postProcessor;
    }
    /**
     * @param Build $build
     * @return void
     */
    public function apply($build)
    {
        foreach ($this->postProcessors as $postProcessor) {
            $postProcessor->apply($build);
        }
    }
}
