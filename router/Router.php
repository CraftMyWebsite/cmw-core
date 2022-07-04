<?php

namespace CMW\Router;

use Closure;

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
            throw new RouterException('REQUEST_METHOD does not exist');
        }
        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] as $route) {
            if ($route->match($this->url)) {
                return $route->call();
            }
        }
        //TODO ERROR PAGE
        throw new RouterException('No matching routes');
    }

    /**
     * @throws RouterException
     */
    public function url($name, $params = [])
    {
        if (!isset($this->namedRoutes[$name])) {
            //TODO ERROR PAGE
            throw new RouterException('No route matches this name');
        }
        return $this->namedRoutes[$name]->getUrl($params);
    }

}