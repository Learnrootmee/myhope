<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use SplPriorityQueue;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Alias;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
class DecoratorServicePass extends AbstractRecursivePass
{
    /**
     * @param ContainerBuilder $container
     */
    public function process($container)
    {
        $definitions = new SplPriorityQueue();
        $order = \PHP_INT_MAX;
        foreach ($container->getDefinitions() as $id => $definition) {
            if (!($decorated = $definition->getDecoratedService())) {
                continue;
            }
            $definitions->insert([$id, $definition], [$decorated[2], --$order]);
        }
        $decoratingDefinitions = [];
        $tagsToKeep = $container->hasParameter('container.behavior_describing_tags') ? $container->getParameter('container.behavior_describing_tags') : ['container.do_not_inline', 'container.service_locator', 'container.service_subscriber'];
        foreach ($definitions as list($id, $definition)) {
            $decoratedService = $definition->getDecoratedService();
            list($inner, $renamedId) = $decoratedService;
            $invalidBehavior = $decoratedService[3] ?? ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE;
            $definition->setDecoratedService(null);
            if (!$renamedId) {
                $renamedId = $id . '.inner';
            }
            $this->currentId = $renamedId;
            $this->processValue($definition);
            $definition->innerServiceId = $renamedId;
            $definition->decorationOnInvalid = $invalidBehavior;
            if ($container->hasAlias($inner)) {
                $alias = $container->getAlias($inner);
                $public = $alias->isPublic();
                $container->setAlias($renamedId, new Alias((string) $alias, \false));
                $decoratedDefinition = $container->findDefinition($alias);
            } elseif ($container->hasDefinition($inner)) {
                $decoratedDefinition = $container->getDefinition($inner);
                $public = $decoratedDefinition->isPublic();
                $decoratedDefinition->setPublic(\false);
                $container->setDefinition($renamedId, $decoratedDefinition);
                $decoratingDefinitions[$inner] = $decoratedDefinition;
            } elseif (ContainerInterface::IGNORE_ON_INVALID_REFERENCE === $invalidBehavior) {
                $container->removeDefinition($id);
                continue;
            } elseif (ContainerInterface::NULL_ON_INVALID_REFERENCE === $invalidBehavior) {
                $public = $definition->isPublic();
                $decoratedDefinition = null;
            } else {
                throw new ServiceNotFoundException($inner, $id);
            }
            if ($decoratedDefinition && $decoratedDefinition->isSynthetic()) {
                throw new InvalidArgumentException(\sprintf('A synthetic service cannot be decorated: service "%s" cannot decorate "%s".', $id, $inner));
            }
            if (isset($decoratingDefinitions[$inner])) {
                $decoratingDefinition = $decoratingDefinitions[$inner];
                $decoratingTags = $decoratingDefinition->getTags();
                $resetTags = [];
                foreach ($tagsToKeep as $containerTag) {
                    if (isset($decoratingTags[$containerTag])) {
                        $resetTags[$containerTag] = $decoratingTags[$containerTag];
                        unset($decoratingTags[$containerTag]);
                    }
                }
                $definition->setTags(\array_merge($decoratingTags, $definition->getTags()));
                $decoratingDefinition->setTags($resetTags);
                $decoratingDefinitions[$inner] = $definition;
            }
            $container->setAlias($inner, $id)->setPublic($public);
        }
    }
    /**
     * @param mixed $value
     * @return mixed
     * @param bool $isRoot
     */
    protected function processValue($value, $isRoot = \false)
    {
        if ($value instanceof Reference && '.inner' === (string) $value) {
            return new Reference($this->currentId, $value->getInvalidBehavior());
        }
        return parent::processValue($value, $isRoot);
    }
}
