<?php

namespace Staatic\Vendor\Symfony\Component\CssSelector\XPath\Extension;

use Staatic\Vendor\Symfony\Component\CssSelector\Exception\ExpressionErrorException;
use Staatic\Vendor\Symfony\Component\CssSelector\Node\FunctionNode;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\Translator;
use Staatic\Vendor\Symfony\Component\CssSelector\XPath\XPathExpr;
class HtmlExtension extends AbstractExtension
{
    public function __construct(Translator $translator)
    {
        $translator->getExtension('node')->setFlag(NodeExtension::ELEMENT_NAME_IN_LOWER_CASE, \true)->setFlag(NodeExtension::ATTRIBUTE_NAME_IN_LOWER_CASE, \true);
    }
    public function getPseudoClassTranslators() : array
    {
        return ['checked' => [$this, 'translateChecked'], 'link' => [$this, 'translateLink'], 'disabled' => [$this, 'translateDisabled'], 'enabled' => [$this, 'translateEnabled'], 'selected' => [$this, 'translateSelected'], 'invalid' => [$this, 'translateInvalid'], 'hover' => [$this, 'translateHover'], 'visited' => [$this, 'translateVisited']];
    }
    public function getFunctionTranslators() : array
    {
        return ['lang' => [$this, 'translateLang']];
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateChecked($xpath) : XPathExpr
    {
        return $xpath->addCondition('(@checked ' . "and (name(.) = 'input' or name(.) = 'command')" . "and (@type = 'checkbox' or @type = 'radio'))");
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateLink($xpath) : XPathExpr
    {
        return $xpath->addCondition("@href and (name(.) = 'a' or name(.) = 'link' or name(.) = 'area')");
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateDisabled($xpath) : XPathExpr
    {
        return $xpath->addCondition('(' . '@disabled and' . '(' . "(name(.) = 'input' and @type != 'hidden')" . " or name(.) = 'button'" . " or name(.) = 'select'" . " or name(.) = 'textarea'" . " or name(.) = 'command'" . " or name(.) = 'fieldset'" . " or name(.) = 'optgroup'" . " or name(.) = 'option'" . ')' . ') or (' . "(name(.) = 'input' and @type != 'hidden')" . " or name(.) = 'button'" . " or name(.) = 'select'" . " or name(.) = 'textarea'" . ')' . ' and ancestor::fieldset[@disabled]');
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateEnabled($xpath) : XPathExpr
    {
        return $xpath->addCondition('(' . '@href and (' . "name(.) = 'a'" . " or name(.) = 'link'" . " or name(.) = 'area'" . ')' . ') or (' . '(' . "name(.) = 'command'" . " or name(.) = 'fieldset'" . " or name(.) = 'optgroup'" . ')' . ' and not(@disabled)' . ') or (' . '(' . "(name(.) = 'input' and @type != 'hidden')" . " or name(.) = 'button'" . " or name(.) = 'select'" . " or name(.) = 'textarea'" . " or name(.) = 'keygen'" . ')' . ' and not (@disabled or ancestor::fieldset[@disabled])' . ') or (' . "name(.) = 'option' and not(" . '@disabled or ancestor::optgroup[@disabled]' . ')' . ')');
    }
    /**
     * @param XPathExpr $xpath
     * @param FunctionNode $function
     */
    public function translateLang($xpath, $function) : XPathExpr
    {
        $arguments = $function->getArguments();
        foreach ($arguments as $token) {
            if (!($token->isString() || $token->isIdentifier())) {
                throw new ExpressionErrorException('Expected a single string or identifier for :lang(), got ' . \implode(', ', $arguments));
            }
        }
        return $xpath->addCondition(\sprintf('ancestor-or-self::*[@lang][1][starts-with(concat(' . "translate(@%s, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), '-')" . ', %s)]', 'lang', Translator::getXpathLiteral(\strtolower($arguments[0]->getValue()) . '-')));
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateSelected($xpath) : XPathExpr
    {
        return $xpath->addCondition("(@selected and name(.) = 'option')");
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateInvalid($xpath) : XPathExpr
    {
        return $xpath->addCondition('0');
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateHover($xpath) : XPathExpr
    {
        return $xpath->addCondition('0');
    }
    /**
     * @param XPathExpr $xpath
     */
    public function translateVisited($xpath) : XPathExpr
    {
        return $xpath->addCondition('0');
    }
    public function getName() : string
    {
        return 'html';
    }
}
