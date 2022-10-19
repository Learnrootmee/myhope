<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension;

use Staatic\Vendor\Symfony\Component\CssSelector\Node\SelectorNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\CombinedSelectorNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\FunctionNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\PseudoNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\AttributeNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\ClassNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\HashNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\ElementNode;
use Staatic\Vendor\Symfony\Component\CssSelector\Node;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\Translator;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\XPathExpr;
class NodeExtension extends AbstractExtension
{
    const ELEMENT_NAME_IN_LOWER_CASE = 1;
    const ATTRIBUTE_NAME_IN_LOWER_CASE = 2;
    const ATTRIBUTE_VALUE_IN_LOWER_CASE = 4;
    /**
     * @var int
     */
    private $flags;
    public function __construct(int $flags = 0)
    {
        $this->flags = $flags;
    }
    /**
     * @return $this
     * @param int $flag
     * @param bool $on
     */
    public function setFlag($flag, $on)
    {
        if ($on && !$this->hasFlag($flag)) {
            $this->flags += $flag;
        }
        if (!$on && $this->hasFlag($flag)) {
            $this->flags -= $flag;
        }
        return $this;
    }
    /**
     * @param int $flag
     */
    public function hasFlag($flag) : bool
    {
        return (bool) ($this->flags & $flag);
    }
    public function getNodeTranslators() : array
    {
        return ['Selector' => [$this, 'translateSelector'], 'CombinedSelector' => [$this, 'translateCombinedSelector'], 'Negation' => [$this, 'translateNegation'], 'Function' => [$this, 'translateFunction'], 'Pseudo' => [$this, 'translatePseudo'], 'Attribute' => [$this, 'translateAttribute'], 'Class' => [$this, 'translateClass'], 'Hash' => [$this, 'translateHash'], 'Element' => [$this, 'translateElement']];
    }
    /**
     * @param SelectorNode $node
     * @param Translator $translator
     */
    public function translateSelector($node, $translator) : XPathExpr
    {
        return $translator->nodeToXPath($node->getTree());
    }
    /**
     * @param CombinedSelectorNode $node
     * @param Translator $translator
     */
    public function translateCombinedSelector($node, $translator) : XPathExpr
    {
        return $translator->addCombination($node->getCombinator(), $node->getSelector(), $node->getSubSelector());
    }
    /**
     * @param \Staatic\Vendor\Symfony\Component\CssSelector\Node\NegationNode $node
     * @param Translator $translator
     */
    public function translateNegation($node, $translator) : XPathExpr
    {
        $xpath = $translator->nodeToXPath($node->getSelector());
        $subXpath = $translator->nodeToXPath($node->getSubSelector());
        $subXpath->addNameTest();
        if ($subXpath->getCondition()) {
            return $xpath->addCondition(\sprintf('not(%s)', $subXpath->getCondition()));
        }
        return $xpath->addCondition('0');
    }
    /**
     * @param FunctionNode $node
     * @param Translator $translator
     */
    public function translateFunction($node, $translator) : XPathExpr
    {
        $xpath = $translator->nodeToXPath($node->getSelector());
        return $translator->addFunction($xpath, $node);
    }
    /**
     * @param PseudoNode $node
     * @param Translator $translator
     */
    public function translatePseudo($node, $translator) : XPathExpr
    {
        $xpath = $translator->nodeToXPath($node->getSelector());
        return $translator->addPseudoClass($xpath, $node->getIdentifier());
    }
    /**
     * @param AttributeNode $node
     * @param Translator $translator
     */
    public function translateAttribute($node, $translator) : XPathExpr
    {
        $name = $node->getAttribute();
        $safe = $this->isSafeName($name);
        if ($this->hasFlag(self::ATTRIBUTE_NAME_IN_LOWER_CASE)) {
            $name = \strtolower($name);
        }
        if ($node->getNamespace()) {
            $name = \sprintf('%s:%s', $node->getNamespace(), $name);
            $safe = $safe && $this->isSafeName($node->getNamespace());
        }
        $attribute = $safe ? '@' . $name : \sprintf('attribute::*[name() = %s]', Translator::getXpathLiteral($name));
        $value = $node->getValue();
        $xpath = $translator->nodeToXPath($node->getSelector());
        if ($this->hasFlag(self::ATTRIBUTE_VALUE_IN_LOWER_CASE)) {
            $value = \strtolower($value);
        }
        return $translator->addAttributeMatching($xpath, $node->getOperator(), $attribute, $value);
    }
    /**
     * @param ClassNode $node
     * @param Translator $translator
     */
    public function translateClass($node, $translator) : XPathExpr
    {
        $xpath = $translator->nodeToXPath($node->getSelector());
        return $translator->addAttributeMatching($xpath, '~=', '@class', $node->getName());
    }
    /**
     * @param HashNode $node
     * @param Translator $translator
     */
    public function translateHash($node, $translator) : XPathExpr
    {
        $xpath = $translator->nodeToXPath($node->getSelector());
        return $translator->addAttributeMatching($xpath, '=', '@id', $node->getId());
    }
    /**
     * @param ElementNode $node
     */
    public function translateElement($node) : XPathExpr
    {
        $element = $node->getElement();
        if ($element && $this->hasFlag(self::ELEMENT_NAME_IN_LOWER_CASE)) {
            $element = \strtolower($element);
        }
        if ($element) {
            $safe = $this->isSafeName($element);
        } else {
            $element = '*';
            $safe = \true;
        }
        if ($node->getNamespace()) {
            $element = \sprintf('%s:%s', $node->getNamespace(), $element);
            $safe = $safe && $this->isSafeName($node->getNamespace());
        }
        $xpath = new XPathExpr('', $element);
        if (!$safe) {
            $xpath->addNameTest();
        }
        return $xpath;
    }
    public function getName() : string
    {
        return 'node';
    }
    private function isSafeName(string $name) : bool
    {
        return 0 < \preg_match('~^[a-zA-Z_][a-zA-Z0-9_.-]*$~', $name);
    }
}
