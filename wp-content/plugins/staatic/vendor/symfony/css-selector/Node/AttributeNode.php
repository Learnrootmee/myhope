<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\Node;

class AttributeNode extends AbstractNode
{
    private $selector;
    /**
     * @var string|null
     */
    private $namespace;
    /**
     * @var string
     */
    private $attribute;
    /**
     * @var string
     */
    private $operator;
    /**
     * @var string|null
     */
    private $value;
    /**
     * @param string|null $namespace
     * @param string|null $value
     */
    public function __construct(NodeInterface $selector, $namespace, string $attribute, string $operator, $value)
    {
        $this->selector = $selector;
        $this->namespace = $namespace;
        $this->attribute = $attribute;
        $this->operator = $operator;
        $this->value = $value;
    }
    public function getSelector() : NodeInterface
    {
        return $this->selector;
    }
    /**
     * @return string|null
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
    public function getAttribute() : string
    {
        return $this->attribute;
    }
    public function getOperator() : string
    {
        return $this->operator;
    }
    /**
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }
    public function getSpecificity() : Specificity
    {
        return $this->selector->getSpecificity()->plus(new Specificity(0, 1, 0));
    }
    public function __toString() : string
    {
        $attribute = $this->namespace ? $this->namespace . '|' . $this->attribute : $this->attribute;
        return 'exists' === $this->operator ? \sprintf('%s[%s[%s]]', $this->getNodeName(), $this->selector, $attribute) : \sprintf("%s[%s[%s %s '%s']]", $this->getNodeName(), $this->selector, $attribute, $this->operator, $this->value);
    }
}
