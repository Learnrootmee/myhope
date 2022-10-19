<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

use Closure;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Staatic\Vendor\Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
class ExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    /**
     * @var Closure|null
     */
    private $serviceCompiler;
    public function __construct(callable $serviceCompiler = null)
    {
        $callable = $serviceCompiler;
        $this->serviceCompiler = null !== $serviceCompiler && !$serviceCompiler instanceof Closure ? function () use ($callable) {
            return $callable(...func_get_args());
        } : $serviceCompiler;
    }
    public function getFunctions() : array
    {
        return [new ExpressionFunction('service', $this->serviceCompiler ?? function ($arg) {
            return \sprintf('$this->get(%s)', $arg);
        }, function (array $variables, $value) {
            return $variables['container']->get($value);
        }), new ExpressionFunction('parameter', function ($arg) {
            return \sprintf('$this->getParameter(%s)', $arg);
        }, function (array $variables, $value) {
            return $variables['container']->getParameter($value);
        })];
    }
}
