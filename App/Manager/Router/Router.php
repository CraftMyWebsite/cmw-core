<?php

namespace CMW\Manager\Router;

use CMW\Manager\Loader\Loader;
use CMW\Manager\Router\Implementations\BaseRouterImplementation;
use function array_filter;
use function array_reduce;
use function http_build_query;
use const ARRAY_FILTER_USE_KEY;

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

    /**
     * @return array
     * <p>Get current url query params. Ignore "url" parameter.</p>
     */
    public static function extractQueryParams(): array
    {
        return array_filter($_GET, static function ($key) {
            return $key !== 'url';
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * @param array $params
     * @return string
     * <p>Return the query parameters, ex: <b>?value=abc&toto=123</b></p>
     */
    public static function buildQueryParams(array $params): string
    {
        return "?" . http_build_query($params);
    }
}
