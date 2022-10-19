<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Attribute;

use Attribute;
#[Attribute(Attribute::TARGET_CLASS)]
class AsTaggedItem
{
    /**
     * @var string|null
     */
    public $index;
    /**
     * @var int|null
     */
    public $priority;
    /**
     * @param string|null $index
     * @param int|null $priority
     */
    public function __construct($index = null, $priority = null)
    {
        $this->index = $index;
        $this->priority = $priority;
    }
}
