<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition;

use InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
class EnumNode extends ScalarNode
{
    /**
     * @var mixed[]
     */
    private $values;
    /**
     * @param string|null $name
     */
    public function __construct($name, NodeInterface $parent = null, array $values = [], string $pathSeparator = BaseNode::DEFAULT_PATH_SEPARATOR)
    {
        $values = \array_unique($values);
        if (empty($values)) {
            throw new InvalidArgumentException('$values must contain at least one element.');
        }
        parent::__construct($name, $parent, $pathSeparator);
        $this->values = $values;
    }
    public function getValues()
    {
        return $this->values;
    }
    /**
     * @param mixed $value
     * @return mixed
     */
    protected function finalizeValue($value)
    {
        $value = parent::finalizeValue($value);
        if (!\in_array($value, $this->values, \true)) {
            $ex = new InvalidConfigurationException(\sprintf('The value %s is not allowed for path "%s". Permissible values: %s', \json_encode($value), $this->getPath(), \implode(', ', \array_map('json_encode', $this->values))));
            $ex->setPath($this->getPath());
            throw $ex;
        }
        return $value;
    }
    protected function allowPlaceholders() : bool
    {
        return \false;
    }
}
