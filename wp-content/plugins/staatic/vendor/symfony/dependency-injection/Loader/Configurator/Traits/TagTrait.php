<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
trait TagTrait
{
    /**
     * @return $this
     * @param string $name
     * @param mixed[] $attributes
     */
    public final function tag($name, $attributes = [])
    {
        if ('' === $name) {
            throw new InvalidArgumentException(\sprintf('The tag name for service "%s" must be a non-empty string.', $this->id));
        }
        foreach ($attributes as $attribute => $value) {
            if (!\is_scalar($value) && null !== $value) {
                throw new InvalidArgumentException(\sprintf('A tag attribute must be of a scalar-type for service "%s", tag "%s", attribute "%s".', $this->id, $name, $attribute));
            }
        }
        $this->definition->addTag($name, $attributes);
        return $this;
    }
}
