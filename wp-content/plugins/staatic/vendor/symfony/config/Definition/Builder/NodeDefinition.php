<?php

namespace Staatic\Vendor\Symfony\Component\Config\Definition\Builder;

use Staatic\Vendor\Symfony\Component\Config\Definition\BaseNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;
use Staatic\Vendor\Symfony\Component\Config\Definition\NodeInterface;
abstract class NodeDefinition implements NodeParentInterface
{
    protected $name;
    protected $normalization;
    protected $validation;
    protected $defaultValue;
    protected $default = \false;
    protected $required = \false;
    protected $deprecation = [];
    protected $merge;
    protected $allowEmptyValue = \true;
    protected $nullEquivalent;
    protected $trueEquivalent = \true;
    protected $falseEquivalent = \false;
    protected $pathSeparator = BaseNode::DEFAULT_PATH_SEPARATOR;
    protected $parent;
    protected $attributes = [];
    /**
     * @param string|null $name
     */
    public function __construct($name, NodeParentInterface $parent = null)
    {
        $this->parent = $parent;
        $this->name = $name;
    }
    /**
     * @return $this
     * @param \Staatic\Vendor\Symfony\Component\Config\Definition\Builder\NodeParentInterface $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        return $this;
    }
    /**
     * @return $this
     * @param string $info
     */
    public function info($info)
    {
        return $this->attribute('info', $info);
    }
    /**
     * @param string|mixed[] $example
     * @return $this
     */
    public function example($example)
    {
        return $this->attribute('example', $example);
    }
    /**
     * @param mixed $value
     * @return $this
     * @param string $key
     */
    public function attribute($key, $value)
    {
        $this->attributes[$key] = $value;
        return $this;
    }
    /**
     * @return \Staatic\Vendor\Symfony\Component\Config\Definition\Builder\NodeParentInterface|NodeBuilder|\Staatic\Vendor\Symfony\Component\Config\Definition\Builder\NodeDefinition|ArrayNodeDefinition|\Staatic\Vendor\Symfony\Component\Config\Definition\Builder\VariableNodeDefinition|null
     */
    public function end()
    {
        return $this->parent;
    }
    /**
     * @param bool $forceRootNode
     */
    public function getNode($forceRootNode = \false) : NodeInterface
    {
        if ($forceRootNode) {
            $this->parent = null;
        }
        if (null !== $this->normalization) {
            $this->normalization->before = ExprBuilder::buildExpressions($this->normalization->before);
        }
        if (null !== $this->validation) {
            $this->validation->rules = ExprBuilder::buildExpressions($this->validation->rules);
        }
        $node = $this->createNode();
        if ($node instanceof BaseNode) {
            $node->setAttributes($this->attributes);
        }
        return $node;
    }
    /**
     * @param mixed $value
     * @return $this
     */
    public function defaultValue($value)
    {
        $this->default = \true;
        $this->defaultValue = $value;
        return $this;
    }
    /**
     * @return $this
     */
    public function isRequired()
    {
        $this->required = \true;
        return $this;
    }
    /**
     * @return $this
     * @param string $package
     * @param string $version
     * @param string $message
     */
    public function setDeprecated($package, $version, $message = 'The child node "%node%" at path "%path%" is deprecated.')
    {
        $this->deprecation = ['package' => $package, 'version' => $version, 'message' => $message];
        return $this;
    }
    /**
     * @param mixed $value
     * @return $this
     */
    public function treatNullLike($value)
    {
        $this->nullEquivalent = $value;
        return $this;
    }
    /**
     * @param mixed $value
     * @return $this
     */
    public function treatTrueLike($value)
    {
        $this->trueEquivalent = $value;
        return $this;
    }
    /**
     * @param mixed $value
     * @return $this
     */
    public function treatFalseLike($value)
    {
        $this->falseEquivalent = $value;
        return $this;
    }
    /**
     * @return $this
     */
    public function defaultNull()
    {
        return $this->defaultValue(null);
    }
    /**
     * @return $this
     */
    public function defaultTrue()
    {
        return $this->defaultValue(\true);
    }
    /**
     * @return $this
     */
    public function defaultFalse()
    {
        return $this->defaultValue(\false);
    }
    public function beforeNormalization() : ExprBuilder
    {
        return $this->normalization()->before();
    }
    /**
     * @return $this
     */
    public function cannotBeEmpty()
    {
        $this->allowEmptyValue = \false;
        return $this;
    }
    public function validate() : ExprBuilder
    {
        return $this->validation()->rule();
    }
    /**
     * @return $this
     * @param bool $deny
     */
    public function cannotBeOverwritten($deny = \true)
    {
        $this->merge()->denyOverwrite($deny);
        return $this;
    }
    protected function validation() : ValidationBuilder
    {
        if (null === $this->validation) {
            $this->validation = new ValidationBuilder($this);
        }
        return $this->validation;
    }
    protected function merge() : MergeBuilder
    {
        if (null === $this->merge) {
            $this->merge = new MergeBuilder($this);
        }
        return $this->merge;
    }
    protected function normalization() : NormalizationBuilder
    {
        if (null === $this->normalization) {
            $this->normalization = new NormalizationBuilder($this);
        }
        return $this->normalization;
    }
    protected abstract function createNode() : NodeInterface;
    /**
     * @return $this
     * @param string $separator
     */
    public function setPathSeparator($separator)
    {
        if ($this instanceof ParentNodeDefinitionInterface) {
            foreach ($this->getChildNodeDefinitions() as $child) {
                $child->setPathSeparator($separator);
            }
        }
        $this->pathSeparator = $separator;
        return $this;
    }
}
