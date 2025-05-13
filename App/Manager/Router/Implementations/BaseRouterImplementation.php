<?php

namespace CMW\Manager\Router\Implementations;


use Closure;
use CMW\Manager\Error\ErrorManager;
use CMW\Manager\Metrics\VisitsMetricsManager;
use CMW\Manager\Requests\HttpMethodsType;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\IRouter;
use CMW\Manager\Router\Link;
use CMW\Manager\Router\Route;
use CMW\Manager\Router\RouterException;
use CMW\Manager\Security\RateLimiter;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;
use ReflectionException;
use ReflectionMethod;
use function is_null;
use function str_replace;
use function strtolower;
use function uniqid;

class BaseRouterImplementation implements IRouter
{
    private string $url;
    private static self $_instance;

    /**
     * @var Route[] $routes
     */
    private array $routes = [];

    /**
     * @var Route[] $namedRoutes
     */
    private array $namedRoutes = [];

    private static ?Route $actualRoute = null;

    private string $groupPattern;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public static function getInstance(): self
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self($_GET['url'] ?? '');
        }

        return self::$_instance;
    }

    public function weight(): int
    {
        return 1;
    }

    private function registerGetRoute(Link $link, ReflectionMethod $method): Route
    {
        return $this->get($link->getPath(), function (...$values) use ($method) {
            $request = new Request(
                url: $this->url,
                method: HttpMethodsType::GET,
                params: $this->getRouteByUrl($this->url)?->getParams() ?? [],
                data: $_GET ?? [],
                emitUrl: $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

            $this->callRegisteredRoute($method, $request, ...$values);
        }, name: $link->getName(), weight: $link->getWeight());
    }

    private function registerPostRoute(Link $link, ReflectionMethod $method): Route
    {
        return $this->post($link->getPath(), function (...$values) use ($link, $method) {
            if ($link->isSecure()) {
                // Check security before send post request
                $security = new SecurityManager();

                if (!empty($security->validate())) {
                    $security->unsetToken();  // Remove the token from the session...
                } else {
                    throw new RouterException('Wrong token, try again sir.', 403);
                }
            }

            $request = new Request(
                url: $this->url,
                method: HttpMethodsType::POST,
                params: $this->getRouteByUrl($this->url)?->getParams() ?? [],
                data: $_POST ?? [],
                emitUrl: $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            );

            $this->callRegisteredRoute($method, $request, ...$values);
        }, name: $link->getName(), weight: $link->getWeight());
    }

    /**
     * @throws ReflectionException
     */
    private function callRegisteredRoute(ReflectionMethod $method, Request $request, string ...$values): void
    {
        $classInstance = $method->getDeclaringClass()->getMethod('getInstance')->invoke(null);
        if (!$method->isPrivate()) {
            ErrorManager::showCustomWarning('Warning', "You can't call a public method in a route.");
        }
        RateLimiter::getInstance()->init();

        $args = [...$values, $request];

        $method->invoke($classInstance, ...$args);
    }

    private function generateRouteName(ReflectionMethod $method): string
    {
        $class = strtolower(str_replace('Controller', '', $method->getDeclaringClass()->getShortName()));
        return "$class.{$method->getName()}";
    }

    public static function getActualRoute(): ?Route
    {
        return self::$actualRoute;
    }

    public static function setActualRoute(?Route $actualRoute): void
    {
        self::$actualRoute = $actualRoute;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return Route[]
     */
    public function getRoutesByName(): array
    {
        return $this->namedRoutes;
    }

    public function addRoute(Route $route, $method): void
    {
        $this->routes[$method][] = $route;
        $this->namedRoutes[$route->getName()] = &$route;
    }

    public function get($path, $callable, $name = '', $weight = 1): Route
    {
        return $this->add($path, $callable, $name, 'GET', $weight);
    }

    public function post($path, $callable, $name = '', $weight = 1): Route
    {
        return $this->add($path, $callable, $name, 'POST', $weight);
    }

    private function add($path, $callable, $name, $method, $weight = 1): Route
    {
        if (!empty($this->groupPattern)) {
            $path = $this->groupPattern . $path;
        }

        $name = $name ?: uniqid('route-', true);

        $route = new Route($path, $callable, $weight, $name);

        $this->addRoute($route, $method);

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
    public function listen(): mixed
    {
        if (!isset($this->getRoutes()[$_SERVER['REQUEST_METHOD']])) {
            throw new RouterException('REQUEST_METHOD does not exist', 500);
        }

        $matchedRoute = $this->getRouteByUrl($this->url);

        if (is_null($matchedRoute)) {
            throw new RouterException('No matching routes', 404);
        }

        // Ignore metrics when we are on installer
        if (!Website::isContainingRoute('installer')) {
            (new VisitsMetricsManager())->registerVisit($matchedRoute);
        }

        self::setActualRoute($matchedRoute);
        return $matchedRoute->call();
    }

    public function getRouteByUrl(string $url, ?HttpMethodsType $method = null): ?Route
    {
        if ($method === null) {
            $method = HttpMethodsType::fromName($_SERVER['REQUEST_METHOD']);
        }

        $matchedRoute = null;

        foreach ($this->getRoutes()[$method->name] as $route) {
            /** @var Route $route */
            if ($route->match($url)) {
                if (is_null($matchedRoute) || $route->getWeight() > $matchedRoute->getWeight()) {
                    $matchedRoute = $route;
                }
            }
        }

        return $matchedRoute;
    }

    public function getRouteByName($name): ?Route
    {
        return $this->namedRoutes[$name] ?? null;
    }

    public function registerRoute(Link $link, ReflectionMethod $method): void
    {
        if (!is_null($link->getScope())) {
            $this->scope($link->getScope(), function () use ($link, $method) {
                $newLink = new Link($link->getPath(), $link->getMethod(), $link->getVariables(), null, $link->getWeight(), $link->getName(), $link->isSecure());
                $this->registerRoute($newLink, $method);
            });

            return;
        }

        $link->setName($this->generateRouteName($method));

        $router = match ($link->getMethod()) {
            Link::GET => $this->registerGetRoute($link, $method),
            Link::POST => $this->registerPostRoute($link, $method),
        };

        $regexValues = $link->getVariables();
        foreach ($regexValues as $value => $regex) {
            $router->with($value, $regex);
        }
    }
}
