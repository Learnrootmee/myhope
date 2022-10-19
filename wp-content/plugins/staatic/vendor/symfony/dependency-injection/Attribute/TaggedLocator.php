<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute;

use Attribute;
#[Attribute(Attribute::TARGET_PARAMETER)]
class TaggedLocator
{
    /**
     * @var string
     */
    public $tag;
    /**
     * @var string|null
     */
    public $indexAttribute;
    /**
     * @var string|null
     */
    public $defaultIndexMethod;
    /**
     * @var string|null
     */
    public $defaultPriorityMethod;
    /**
     * @param string|null $indexAttribute
     * @param string|null $defaultIndexMethod
     * @param string|null $defaultPriorityMethod
     */
    public function __construct(string $tag, $indexAttribute = null, $defaultIndexMethod = null, $defaultPriorityMethod = null)
    {
        $this->tag = $tag;
        $this->indexAttribute = $indexAttribute;
        $this->defaultIndexMethod = $defaultIndexMethod;
        $this->defaultPriorityMethod = $defaultPriorityMethod;
    }
}
