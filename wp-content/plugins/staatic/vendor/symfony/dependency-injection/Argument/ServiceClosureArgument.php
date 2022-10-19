<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Argument;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
class ServiceClosureArgument implements ArgumentInterface
{
    /**
     * @var mixed[]
     */
    private $values;
    public function __construct(Reference $reference)
    {
        $this->values = [$reference];
    }
    public function getValues() : array
    {
        return $this->values;
    }
    /**
     * @param mixed[] $values
     */
    public function setValues($values)
    {
        if ([0] !== \array_keys($values) || !($values[0] instanceof Reference || null === $values[0])) {
            throw new InvalidArgumentException('A ServiceClosureArgument must hold one and only one Reference.');
        }
        $this->values = $values;
    }
}
