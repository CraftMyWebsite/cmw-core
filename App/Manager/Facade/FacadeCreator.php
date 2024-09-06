<?php

namespace CMW\Manager\Facade;

use Closure;

class FacadeCreator
{
    public static function createFunction(string $name, Closure $functionClosure): void
    {
        $functionName = $name;

        if (!function_exists($name)) {
            $GLOBALS[$functionName] = $functionClosure;
        }
    }
}
