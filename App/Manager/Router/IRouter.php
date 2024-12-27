<?php

namespace CMW\Manager\Router;

use CMW\Manager\Requests\HttpMethodsType;
use ReflectionMethod;

/**
 * @desc Interface for Router
 */
interface IRouter
{
    /**
     * @return int
     */
    public function weight(): int;

    /**
     * @return self
     */
    public static function getInstance(): self;

    /**
     * @return mixed
     */
    public function listen(): mixed;

    /**
     * @param Link $link
     * @param ReflectionMethod $method
     * @return void
     */
    public function registerRoute(Link $link, ReflectionMethod $method): void;

    /**
     * @param string $path
     * @param callable $callable
     * @param string $name
     * @param int $weight
     * @return Route
     */
    public function get(string $path, callable $callable, string $name, int $weight): Route;

    /**
     * @param string $path
     * @param callable $callable
     * @param string $name
     * @param int $weight
     * @return Route
     */
    public function post(string $path, callable $callable, string $name, int $weight): Route;

    /**
     * @param string $url
     * @param HttpMethodsType|null $method
     * @return Route|null
     */
    public function getRouteByUrl(string $url, ?HttpMethodsType $method = null): ?Route;
}