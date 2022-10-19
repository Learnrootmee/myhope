<?php

namespace Staatic\Vendor\Symfony\Component\Config\Builder;

use Closure;
use LogicException;
use RuntimeException;
use ReflectionProperty;
use ReflectionException;
use Staatic\Vendor\Symfony\Component\Config\Definition\ArrayNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\BooleanNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\ConfigurationInterface;
use Staatic\Vendor\Symfony\Component\Config\Definition\EnumNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Staatic\Vendor\Symfony\Component\Config\Definition\FloatNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\IntegerNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\NodeInterface;
use Staatic\Vendor\Symfony\Component\Config\Definition\PrototypedArrayNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\ScalarNode;
use Staatic\Vendor\Symfony\Component\Config\Definition\VariableNode;
use Staatic\Vendor\Symfony\Component\Config\Loader\ParamConfigurator;
class ConfigBuilderGenerator implements ConfigBuilderGeneratorInterface
{
    /**
     * @var mixed[]
     */
    private $classes = [];
    /**
     * @var string
     */
    private $outputDir;
    public function __construct(string $outputDir)
    {
        $this->outputDir = $outputDir;
    }
    /**
     * @param \Staatic\Vendor\Symfony\Component\Config\Definition\ConfigurationInterface $configuration
     */
    public function build($configuration) : Closure
    {
        $this->classes = [];
        $rootNode = $configuration->getConfigTreeBuilder()->buildTree();
        $rootClass = new ClassBuilder('Staatic\\Vendor\\Symfony\\Config', $rootNode->getName());
        $path = $this->getFullPath($rootClass);
        if (!\is_file($path)) {
            $this->classes[] = $rootClass;
            $this->buildNode($rootNode, $rootClass, $this->getSubNamespace($rootClass));
            $rootClass->addImplements(ConfigBuilderInterface::class);
            $rootClass->addMethod('getExtensionAlias', '
public function NAME(): string
{
    return \'ALIAS\';
}', ['ALIAS' => $rootNode->getPath()]);
            $this->writeClasses();
        }
        $callable = function () use($path, $rootClass) {
            require_once $path;
            $className = $rootClass->getFqcn();
            return new $className();
        };
        $loader = function () use ($callable) {
            return $callable(...func_get_args());
        };
        return $loader;
    }
    private function getFullPath(ClassBuilder $class) : string
    {
        $directory = $this->outputDir . \DIRECTORY_SEPARATOR . $class->getDirectory();
        if (!\is_dir($directory)) {
            @\mkdir($directory, 0777, \true);
        }
        return $directory . \DIRECTORY_SEPARATOR . $class->getFilename();
    }
    /**
     * @return void
     */
    private function writeClasses()
    {
        foreach ($this->classes as $class) {
            $this->buildConstructor($class);
            $this->buildToArray($class);
            if ($class->getProperties()) {
                $class->addProperty('_usedProperties', null, '[]');
            }
            $this->buildSetExtraKey($class);
            \file_put_contents($this->getFullPath($class), $class->build());
        }
        $this->classes = [];
    }
    /**
     * @return void
     */
    private function buildNode(NodeInterface $node, ClassBuilder $class, string $namespace)
    {
        if (!$node instanceof ArrayNode) {
            throw new LogicException('The node was expected to be an ArrayNode. This Configuration includes an edge case not supported yet.');
        }
        foreach ($node->getChildren() as $child) {
            switch (\true) {
                case $child instanceof ScalarNode:
                    $this->handleScalarNode($child, $class);
                    break;
                case $child instanceof PrototypedArrayNode:
                    $this->handlePrototypedArrayNode($child, $class, $namespace);
                    break;
                case $child instanceof VariableNode:
                    $this->handleVariableNode($child, $class);
                    break;
                case $child instanceof ArrayNode:
                    $this->handleArrayNode($child, $class, $namespace);
                    break;
                default:
                    throw new RuntimeException(\sprintf('Unknown node "%s".', \get_class($child)));
            }
        }
    }
    /**
     * @return void
     */
    private function handleArrayNode(ArrayNode $node, ClassBuilder $class, string $namespace)
    {
        $childClass = new ClassBuilder($namespace, $node->getName());
        $childClass->setAllowExtraKeys($node->shouldIgnoreExtraKeys());
        $class->addRequire($childClass);
        $this->classes[] = $childClass;
        $hasNormalizationClosures = $this->hasNormalizationClosures($node);
        $property = $class->addProperty($node->getName(), $this->getType($childClass->getFqcn(), $hasNormalizationClosures));
        $body = $hasNormalizationClosures ? '
/**
 * @return CLASS|$this
 */
public function NAME(mixed $value = []): CLASS|static
{
    if (!\\is_array($value)) {
        $this->_usedProperties[\'PROPERTY\'] = true;
        $this->PROPERTY = $value;

        return $this;
    }

    if (!$this->PROPERTY instanceof CLASS) {
        $this->_usedProperties[\'PROPERTY\'] = true;
        $this->PROPERTY = new CLASS($value);
    } elseif (0 < \\func_num_args()) {
        throw new InvalidConfigurationException(\'The node created by "NAME()" has already been initialized. You cannot pass values the second time you call NAME().\');
    }

    return $this->PROPERTY;
}' : '
public function NAME(array $value = []): CLASS
{
    if (null === $this->PROPERTY) {
        $this->_usedProperties[\'PROPERTY\'] = true;
        $this->PROPERTY = new CLASS($value);
    } elseif (0 < \\func_num_args()) {
        throw new InvalidConfigurationException(\'The node created by "NAME()" has already been initialized. You cannot pass values the second time you call NAME().\');
    }

    return $this->PROPERTY;
}';
        $class->addUse(InvalidConfigurationException::class);
        $class->addMethod($node->getName(), $body, ['PROPERTY' => $property->getName(), 'CLASS' => $childClass->getFqcn()]);
        $this->buildNode($node, $childClass, $this->getSubNamespace($childClass));
    }
    /**
     * @return void
     */
    private function handleVariableNode(VariableNode $node, ClassBuilder $class)
    {
        $comment = $this->getComment($node);
        $property = $class->addProperty($node->getName());
        $class->addUse(ParamConfigurator::class);
        $body = '
/**
COMMENT *
 * @return $this
 */
public function NAME(mixed $valueDEFAULT): static
{
    $this->_usedProperties[\'PROPERTY\'] = true;
    $this->PROPERTY = $value;

    return $this;
}';
        $class->addMethod($node->getName(), $body, ['PROPERTY' => $property->getName(), 'COMMENT' => $comment, 'DEFAULT' => $node->hasDefaultValue() ? ' = ' . \var_export($node->getDefaultValue(), \true) : '']);
    }
    /**
     * @return void
     */
    private function handlePrototypedArrayNode(PrototypedArrayNode $node, ClassBuilder $class, string $namespace)
    {
        $name = $this->getSingularName($node);
        $prototype = $node->getPrototype();
        $methodName = $name;
        $hasNormalizationClosures = $this->hasNormalizationClosures($node) || $this->hasNormalizationClosures($prototype);
        $parameterType = $this->getParameterType($prototype);
        if (null !== $parameterType || $prototype instanceof ScalarNode) {
            $class->addUse(ParamConfigurator::class);
            $property = $class->addProperty($node->getName());
            if (null === ($key = $node->getKeyAttribute())) {
                $body = '
/**
 * @param PHPDOC_TYPE $value
 *
 * @return $this
 */
public function NAME(TYPE $value): static
{
    $this->_usedProperties[\'PROPERTY\'] = true;
    $this->PROPERTY = $value;

    return $this;
}';
                $class->addMethod($node->getName(), $body, ['PROPERTY' => $property->getName(), 'TYPE' => $hasNormalizationClosures ? 'mixed' : 'ParamConfigurator|array', 'PHPDOC_TYPE' => $hasNormalizationClosures ? 'mixed' : \sprintf('ParamConfigurator|list<ParamConfigurator|%s>', '' === $parameterType ? 'mixed' : $parameterType)]);
            } else {
                $body = '
/**
 * @return $this
 */
public function NAME(string $VAR, TYPE $VALUE): static
{
    $this->_usedProperties[\'PROPERTY\'] = true;
    $this->PROPERTY[$VAR] = $VALUE;

    return $this;
}';
                $class->addMethod($methodName, $body, ['PROPERTY' => $property->getName(), 'TYPE' => $hasNormalizationClosures || '' === $parameterType ? 'mixed' : 'ParamConfigurator|' . $parameterType, 'VAR' => '' === $key ? 'key' : $key, 'VALUE' => 'value' === $key ? 'data' : 'value']);
            }
            return;
        }
        $childClass = new ClassBuilder($namespace, $name);
        if ($prototype instanceof ArrayNode) {
            $childClass->setAllowExtraKeys($prototype->shouldIgnoreExtraKeys());
        }
        $class->addRequire($childClass);
        $this->classes[] = $childClass;
        $property = $class->addProperty($node->getName(), $this->getType($childClass->getFqcn() . '[]', $hasNormalizationClosures));
        if (null === ($key = $node->getKeyAttribute())) {
            $body = $hasNormalizationClosures ? '
/**
 * @return CLASS|$this
 */
public function NAME(mixed $value = []): CLASS|static
{
    $this->_usedProperties[\'PROPERTY\'] = true;
    if (!\\is_array($value)) {
        $this->PROPERTY[] = $value;

        return $this;
    }

    return $this->PROPERTY[] = new CLASS($value);
}' : '
public function NAME(array $value = []): CLASS
{
    $this->_usedProperties[\'PROPERTY\'] = true;

    return $this->PROPERTY[] = new CLASS($value);
}';
            $class->addMethod($methodName, $body, ['PROPERTY' => $property->getName(), 'CLASS' => $childClass->getFqcn()]);
        } else {
            $body = $hasNormalizationClosures ? '
/**
 * @return CLASS|$this
 */
public function NAME(string $VAR, mixed $VALUE = []): CLASS|static
{
    if (!\\is_array($VALUE)) {
        $this->_usedProperties[\'PROPERTY\'] = true;
        $this->PROPERTY[$VAR] = $VALUE;

        return $this;
    }

    if (!isset($this->PROPERTY[$VAR]) || !$this->PROPERTY[$VAR] instanceof CLASS) {
        $this->_usedProperties[\'PROPERTY\'] = true;
        $this->PROPERTY[$VAR] = new CLASS($VALUE);
    } elseif (1 < \\func_num_args()) {
        throw new InvalidConfigurationException(\'The node created by "NAME()" has already been initialized. You cannot pass values the second time you call NAME().\');
    }

    return $this->PROPERTY[$VAR];
}' : '
public function NAME(string $VAR, array $VALUE = []): CLASS
{
    if (!isset($this->PROPERTY[$VAR])) {
        $this->_usedProperties[\'PROPERTY\'] = true;
        $this->PROPERTY[$VAR] = new CLASS($VALUE);
    } elseif (1 < \\func_num_args()) {
        throw new InvalidConfigurationException(\'The node created by "NAME()" has already been initialized. You cannot pass values the second time you call NAME().\');
    }

    return $this->PROPERTY[$VAR];
}';
            $class->addUse(InvalidConfigurationException::class);
            $class->addMethod($methodName, $body, ['PROPERTY' => $property->getName(), 'CLASS' => $childClass->getFqcn(), 'VAR' => '' === $key ? 'key' : $key, 'VALUE' => 'value' === $key ? 'data' : 'value']);
        }
        $this->buildNode($prototype, $childClass, $namespace . '\\' . $childClass->getName());
    }
    /**
     * @return void
     */
    private function handleScalarNode(ScalarNode $node, ClassBuilder $class)
    {
        $comment = $this->getComment($node);
        $property = $class->addProperty($node->getName());
        $class->addUse(ParamConfigurator::class);
        $body = '
/**
COMMENT * @return $this
 */
public function NAME($value): static
{
    $this->_usedProperties[\'PROPERTY\'] = true;
    $this->PROPERTY = $value;

    return $this;
}';
        $class->addMethod($node->getName(), $body, ['PROPERTY' => $property->getName(), 'COMMENT' => $comment]);
    }
    /**
     * @return string|null
     */
    private function getParameterType(NodeInterface $node)
    {
        if ($node instanceof BooleanNode) {
            return 'bool';
        }
        if ($node instanceof IntegerNode) {
            return 'int';
        }
        if ($node instanceof FloatNode) {
            return 'float';
        }
        if ($node instanceof EnumNode) {
            return '';
        }
        if ($node instanceof PrototypedArrayNode && $node->getPrototype() instanceof ScalarNode) {
            return 'array';
        }
        if ($node instanceof VariableNode) {
            return '';
        }
        return null;
    }
    private function getComment(VariableNode $node) : string
    {
        $comment = '';
        if ('' !== ($info = (string) $node->getInfo())) {
            $comment .= ' * ' . $info . "\n";
        }
        foreach ((array) ($node->getExample() ?? []) as $example) {
            $comment .= ' * @example ' . $example . "\n";
        }
        if ('' !== ($default = $node->getDefaultValue())) {
            $comment .= ' * @default ' . (null === $default ? 'null' : \var_export($default, \true)) . "\n";
        }
        if ($node instanceof EnumNode) {
            $comment .= \sprintf(' * @param ParamConfigurator|%s $value', \implode('|', \array_map(function ($a) {
                return \var_export($a, \true);
            }, $node->getValues()))) . "\n";
        } else {
            $parameterType = $this->getParameterType($node);
            if (null === $parameterType || '' === $parameterType) {
                $parameterType = 'mixed';
            }
            $comment .= ' * @param ParamConfigurator|' . $parameterType . ' $value' . "\n";
        }
        if ($node->isDeprecated()) {
            $comment .= ' * @deprecated ' . $node->getDeprecation($node->getName(), $node->getParent()->getName())['message'] . "\n";
        }
        return $comment;
    }
    private function getSingularName(PrototypedArrayNode $node) : string
    {
        $name = $node->getName();
        if ('s' !== \substr($name, -1)) {
            return $name;
        }
        $parent = $node->getParent();
        $mappings = $parent instanceof ArrayNode ? $parent->getXmlRemappings() : [];
        foreach ($mappings as $map) {
            if ($map[1] === $name) {
                $name = $map[0];
                break;
            }
        }
        return $name;
    }
    /**
     * @return void
     */
    private function buildToArray(ClassBuilder $class)
    {
        $body = '$output = [];';
        foreach ($class->getProperties() as $p) {
            $code = '$this->PROPERTY';
            if (null !== $p->getType()) {
                if ($p->isArray()) {
                    $code = $p->areScalarsAllowed() ? 'array_map(function ($v) { return $v instanceof CLASS ? $v->toArray() : $v; }, $this->PROPERTY)' : 'array_map(function ($v) { return $v->toArray(); }, $this->PROPERTY)';
                } else {
                    $code = $p->areScalarsAllowed() ? '$this->PROPERTY instanceof CLASS ? $this->PROPERTY->toArray() : $this->PROPERTY' : '$this->PROPERTY->toArray()';
                }
            }
            $body .= \strtr('
    if (isset($this->_usedProperties[\'PROPERTY\'])) {
        $output[\'ORG_NAME\'] = ' . $code . ';
    }', ['PROPERTY' => $p->getName(), 'ORG_NAME' => $p->getOriginalName(), 'CLASS' => $p->getType()]);
        }
        $extraKeys = $class->shouldAllowExtraKeys() ? ' + $this->_extraKeys' : '';
        $class->addMethod('toArray', '
public function NAME(): array
{
    ' . $body . '

    return $output' . $extraKeys . ';
}');
    }
    /**
     * @return void
     */
    private function buildConstructor(ClassBuilder $class)
    {
        $body = '';
        foreach ($class->getProperties() as $p) {
            $code = '$value[\'ORG_NAME\']';
            if (null !== $p->getType()) {
                if ($p->isArray()) {
                    $code = $p->areScalarsAllowed() ? 'array_map(function ($v) { return \\is_array($v) ? new ' . $p->getType() . '($v) : $v; }, $value[\'ORG_NAME\'])' : 'array_map(function ($v) { return new ' . $p->getType() . '($v); }, $value[\'ORG_NAME\'])';
                } else {
                    $code = $p->areScalarsAllowed() ? '\\is_array($value[\'ORG_NAME\']) ? new ' . $p->getType() . '($value[\'ORG_NAME\']) : $value[\'ORG_NAME\']' : 'new ' . $p->getType() . '($value[\'ORG_NAME\'])';
                }
            }
            $body .= \strtr('
    if (array_key_exists(\'ORG_NAME\', $value)) {
        $this->_usedProperties[\'PROPERTY\'] = true;
        $this->PROPERTY = ' . $code . ';
        unset($value[\'ORG_NAME\']);
    }
', ['PROPERTY' => $p->getName(), 'ORG_NAME' => $p->getOriginalName()]);
        }
        if ($class->shouldAllowExtraKeys()) {
            $body .= '
    $this->_extraKeys = $value;
';
        } else {
            $body .= '
    if ([] !== $value) {
        throw new InvalidConfigurationException(sprintf(\'The following keys are not supported by "%s": \', __CLASS__).implode(\', \', array_keys($value)));
    }';
            $class->addUse(InvalidConfigurationException::class);
        }
        $class->addMethod('__construct', '
public function __construct(array $value = [])
{' . $body . '
}');
    }
    /**
     * @return void
     */
    private function buildSetExtraKey(ClassBuilder $class)
    {
        if (!$class->shouldAllowExtraKeys()) {
            return;
        }
        $class->addUse(ParamConfigurator::class);
        $class->addProperty('_extraKeys');
        $class->addMethod('set', '
/**
 * @param ParamConfigurator|mixed $value
 *
 * @return $this
 */
public function NAME(string $key, mixed $value): static
{
    $this->_extraKeys[$key] = $value;

    return $this;
}');
    }
    private function getSubNamespace(ClassBuilder $rootClass) : string
    {
        return \sprintf('%s\\%s', $rootClass->getNamespace(), \substr($rootClass->getName(), 0, -6));
    }
    private function hasNormalizationClosures(NodeInterface $node) : bool
    {
        try {
            $r = new ReflectionProperty($node, 'normalizationClosures');
        } catch (ReflectionException $e) {
            return \false;
        }
        $r->setAccessible(\true);
        return [] !== $r->getValue($node);
    }
    private function getType(string $classType, bool $hasNormalizationClosures) : string
    {
        return $classType . ($hasNormalizationClosures ? '|scalar' : '');
    }
}
