<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection\Compiler;

use Staatic\Vendor\Symfony\Component\DependencyInjection\ContainerInterface;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Definition;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Staatic\Vendor\Symfony\Component\DependencyInjection\Reference;
class DefinitionErrorExceptionPass extends AbstractRecursivePass
{
    /**
     * @param mixed $value
     * @return mixed
     * @param bool $isRoot
     */
    protected function processValue($value, $isRoot = \false)
    {
        if (!$value instanceof Definition || !$value->hasErrors()) {
            return parent::processValue($value, $isRoot);
        }
        if ($isRoot && !$value->isPublic()) {
            $graph = $this->container->getCompiler()->getServiceReferenceGraph();
            $runtimeException = \false;
            foreach ($graph->getNode($this->currentId)->getInEdges() as $edge) {
                if (!$edge->getValue() instanceof Reference || ContainerInterface::RUNTIME_EXCEPTION_ON_INVALID_REFERENCE !== $edge->getValue()->getInvalidBehavior()) {
                    $runtimeException = \false;
                    break;
                }
                $runtimeException = \true;
            }
            if ($runtimeException) {
                return parent::processValue($value, $isRoot);
            }
        }
        $errors = $value->getErrors();
        $message = \reset($errors);
        throw new RuntimeException($message);
    }
}
