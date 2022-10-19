<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\XPath;

class XPathExpr
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $element;
    /**
     * @var string
     */
    private $condition;
    public function __construct(string $path = '', string $element = '*', string $condition = '', bool $starPrefix = \false)
    {
        $this->path = $path;
        $this->element = $element;
        $this->condition = $condition;
        if ($starPrefix) {
            $this->addStarPrefix();
        }
    }
    public function getElement() : string
    {
        return $this->element;
    }
    /**
     * @return $this
     * @param string $condition
     */
    public function addCondition($condition)
    {
        $this->condition = $this->condition ? \sprintf('(%s) and (%s)', $this->condition, $condition) : $condition;
        return $this;
    }
    public function getCondition() : string
    {
        return $this->condition;
    }
    /**
     * @return $this
     */
    public function addNameTest()
    {
        if ('*' !== $this->element) {
            $this->addCondition('name() = ' . Translator::getXpathLiteral($this->element));
            $this->element = '*';
        }
        return $this;
    }
    /**
     * @return $this
     */
    public function addStarPrefix()
    {
        $this->path .= '*/';
        return $this;
    }
    /**
     * @return $this
     * @param string $combiner
     * @param $this $expr
     */
    public function join($combiner, $expr)
    {
        $path = $this->__toString() . $combiner;
        if ('*/' !== $expr->path) {
            $path .= $expr->path;
        }
        $this->path = $path;
        $this->element = $expr->element;
        $this->condition = $expr->condition;
        return $this;
    }
    public function __toString() : string
    {
        $path = $this->path . $this->element;
        $condition = null === $this->condition || '' === $this->condition ? '' : '[' . $this->condition . ']';
        return $path . $condition;
    }
}
