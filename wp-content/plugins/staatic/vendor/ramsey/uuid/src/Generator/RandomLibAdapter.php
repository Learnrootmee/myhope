<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Uuid\Generator;

use Staatic\Vendor\RandomLib\Factory;
use Staatic\Vendor\RandomLib\Generator;
class RandomLibAdapter implements RandomGeneratorInterface
{
    /**
     * @var \Staatic\Vendor\RandomLib\Generator
     */
    private $generator;
    /**
     * @param \Staatic\Vendor\RandomLib\Generator|null $generator
     */
    public function __construct($generator = null)
    {
        if ($generator === null) {
            $factory = new Factory();
            $generator = $factory->getHighStrengthGenerator();
        }
        $this->generator = $generator;
    }
    /**
     * @param int $length
     */
    public function generate($length) : string
    {
        return $this->generator->generate($length);
    }
}
