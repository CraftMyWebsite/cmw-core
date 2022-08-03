<?php

namespace CMW\Router;

use Closure;
use ReflectionMethod;

/**
 * Class: @router
 * @package Core
 * @author CraftMywebsite <contact@craftmywebsite.fr>
 * @version 1.0
 */
class Router
{

    private string $url;
    private array $routes = [];
    private array $namedRoutes = [];
    private string $groupPattern;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function get($path, $callable, $name = null, $weight = 1): Route
    {
        return $this->add($path, $callable, $name, 'GET', $weight);
    }

    public function post($path, $callable, $name = null, $weight = 1): Route
    {
        return $this->add($path, $callable, $name, 'POST', $weight);
    }

    private function add($path, $callable, $name, $method, $weight = 1): Route
    {
        if (!empty($this->groupPattern)) {
            $path = $this->groupPattern . $path;
        }
        $route = new Route($path, $callable, $weight);
        $this->routes[$method][] = $route;
        if (is_string($callable) && $name === null) {
            $name = $callable;
        }
        if ($name) {
            $this->namedRoutes[$name] = $route;
        }
        return $route;
    }

    public function scope($groupPattern, Closure $routes): void
    {
        $this->groupPattern = $groupPattern;
        $routes($this);
        unset($this->groupPattern);
    }

    /**
     * @throws RouterException
     */
    public function listen()
    {
        if (!isset($this->routes[$_SERVER['REQUEST_METHOD']])) {
            throw new RouterException('REQUEST_METHOD does not exist', 500);
        }

        $matchedRoute = $this->getMatchedUrl($this->url);

        if(!is_null($matchedRoute)) {
            return $matchedRoute->call();
        }

        throw new RouterException('No matching routes', 404);
    }

    private function getMatchedUrl(string $url): ?Route
    {
        /** @var $matchedRoute ?Route */
        $matchedRoute = null;
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            /** @var Route $route */
            if ($route->match($url)) {
                if(is_null($matchedRoute?->getWeight()) || $route->getWeight() > $matchedRoute->getWeight()) {
                    $matchedRoute = $route;
                }
            }
        }

        return $matchedRoute;
    }

    /**
     * @throws RouterException
     */
    public function url($name, $params = [])
    {
        if (!isset($this->namedRoutes[$name])) {
            throw new RouterException('No route matches this name', 404);
        }
        return $this->namedRoutes[$name]->getUrl($params);
    }

    public function registerRoute(Link $link, ReflectionMethod $method): void
    {

        if (!is_null($link->getScope())) {
            $this->scope($link->getScope(), function () use ($link, $method) {

                $newLink = new Link($link->getPath(), $link->getMethod(), $link->getVariables(), null, $link->getWeight());
                $this->registerRoute($newLink, $method);
            });

            return;
        }

        $router = match ($link->getMethod()) {
            Link::GET => $this->registerGetRoute($link, $method),
            Link::POST => $this->registerPostRoute($link, $method)
        };


        $regexValues = $link->getVariables();
        foreach ($regexValues as $value => $regex) {

            $router->with($value, $regex);

        }

    }

    private function registerGetRoute(Link $link, ReflectionMethod $method): Route
    {
        return $this->get($link->getPath(), function (...$values) use ($method) {

            $this->callRegisteredRoute($method, ...$values);

        }, weight: $link->getWeight());
    }

    private function registerPostRoute(Link $link, ReflectionMethod $method): Route
    {
        return $this->post($link->getPath(), function (...$values) use ($method) {

            $this->callRegisteredRoute($method, ...$values);

        }, weight: $link->getWeight());
    }


    /**
     * @throws \ReflectionException
     */
    private function callRegisteredRoute(ReflectionMethod $method, string ...$values): void
    {

        $controller = $method->getDeclaringClass()->newInstance();
        $methodName = $method->getName();
        $controller->$methodName(...$values);

    }

}