<?php

namespace Staatic\Vendor\Symfony\Contracts\Service\Attribute;

use Attribute;
use Staatic\Vendor\Symfony\Contracts\Service\ServiceSubscriberTrait;
#[Attribute(Attribute::TARGET_METHOD)]
final class SubscribedService
{
    /**
     * @var string|null
     */
    public $key;
    /**
     * @param string|null $key
     */
    public function __construct($key = null)
    {
        $this->key = $key;
    }
}
