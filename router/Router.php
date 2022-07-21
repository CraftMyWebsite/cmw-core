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

    /**
     * @return Route[]
     */
    public function getAndPost($path, $callableGet, $callablePost): array
    {
        return [$this->get($path, $callableGet), $this->post($path, $callablePost)];
    }

    public function get($path, $callable, $name = null): Route
    {
        return $this->add($path, $callable, $name, 'GET');
    }

    public function post($path, $callable, $name = null): Route
    {
        return $this->add($path, $callable, $name, 'POST');
    }

    private function add($path, $callable, $name, $method): Route
    {
        if (!empty($this->groupPattern)) {
            $path = $this->groupPattern . $path;
        }
        $route = new Route($path, $callable);
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
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            /** @var Route $route */
            if ($route->match($this->url)) {
                return $route->call();
            }
        }
        throw new RouterException('No matching routes', 404);
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

                $newLink = new Link($link->getPath(), $link->getMethod(), $link->getVariables(), null);
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

        });
    }

    private function registerPostRoute(Link $link, ReflectionMethod $method): Route
    {
        return $this->post($link->getPath(), function (...$values) use ($method) {

            $this->callRegisteredRoute($method, ...$values);

        });
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