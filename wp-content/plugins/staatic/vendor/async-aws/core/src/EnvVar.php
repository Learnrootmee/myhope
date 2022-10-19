<?php

declare (strict_types=1);
namespace Staatic\Vendor\AsyncAws\Core;

final class EnvVar
{
    /**
     * @return string|null
     */
    public static function get(string $name)
    {
        if (isset($_ENV[$name])) {
            return $_ENV[$name];
        } elseif (isset($_SERVER[$name]) && 0 !== \strpos($name, 'HTTP_')) {
            return $_SERVER[$name];
        } elseif (\false === ($env = \getenv($name))) {
            return null;
        }
        return $env;
    }
}
