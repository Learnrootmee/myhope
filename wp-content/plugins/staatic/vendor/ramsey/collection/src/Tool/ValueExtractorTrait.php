<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Tool;

use Staatic\Vendor\Ramsey\Collection\Exception\ValueExtractionException;
use function get_class;
use function method_exists;
use function property_exists;
use function sprintf;
trait ValueExtractorTrait
{
    /**
     * @param string $propertyOrMethod
     */
    protected function extractValue($object, $propertyOrMethod)
    {
        if (!\is_object($object)) {
            throw new ValueExtractionException('Unable to extract a value from a non-object');
        }
        if (property_exists($object, $propertyOrMethod)) {
            return $object->{$propertyOrMethod};
        }
        if (method_exists($object, $propertyOrMethod)) {
            return $object->{$propertyOrMethod}();
        }
        throw new ValueExtractionException(sprintf('Method or property "%s" not defined in %s', $propertyOrMethod, get_class($object)));
    }
}
