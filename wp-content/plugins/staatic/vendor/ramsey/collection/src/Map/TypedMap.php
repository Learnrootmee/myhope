<?php

declare (strict_types=1);
namespace Staatic\Vendor\Ramsey\Collection\Map;

use Staatic\Vendor\Ramsey\Collection\Tool\TypeTrait;
class TypedMap extends AbstractTypedMap
{
    use TypeTrait;
    private $keyType;
    private $valueType;
    public function __construct(string $keyType, string $valueType, array $data = [])
    {
        $this->keyType = $keyType;
        $this->valueType = $valueType;
        parent::__construct($data);
    }
    public function getKeyType() : string
    {
        return $this->keyType;
    }
    public function getValueType() : string
    {
        return $this->valueType;
    }
}
