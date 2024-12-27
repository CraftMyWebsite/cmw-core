<?php

namespace CMW\Manager\Router;

use CMW\Manager\Loader\Loader;
use CMW\Manager\Router\Implementations\BaseRouterImplementation;

/**
 * Class: @Router
 * @version 2.0
 */
class Router
{
    private static ?IRouter $_instance = null;

    public static function getInstance(): IRouter
    {
        if (self::$_instance === null) {
            self::$_instance = self::loadRouterInstance();
        }

        return self::$_instance;
    }

    private static function loadRouterInstance(): IRouter
    {
        return self::getHighestImplementation() ?? BaseRouterImplementation::getInstance();
    }

    private static function getHighestImplementation(): ?IRouter
    {
        $implementations = Loader::loadManagerImplementations(IRouter::class, 'Router');

        return array_reduce($implementations, static function (?IRouter $highest, IRouter $current) {
            return ($highest === null || $current->weight() > $highest->weight()) ? $current : $highest;
        });
    }
}
