<?php

namespace CMW\Utils;


use CMW\Router\Route;

class Redirect
{


    private static function getRouteByUrl(string $url): ?Route
    {
        $router = Loader::getRouterInstance();
        $route = $router->getRouteByUrl($url);
        if (is_null($route)) {
            $route = $router->getRouteByName($url);
            if (is_null($route)) {
                return null;
            }
        }

        return $route;
    }

    /**
     * @param string $url Url or Route Name.
     */
    public static function redirect(string $url, array $params = []): void
    {
        $route = self::getRouteByUrl($url);

        if (is_null($route)) {
            return;
        }

        $params = implode(", ", $params);

        http_response_code(302);
        header("Location: " .  Utils::getEnv()->getValue("PATH_SUBFOLDER") . $route->getUrl() . '/' . $params);
    }

    /**
     * @param int $code
     * @return void
     * @desc Redirect to errorPage
     */
    public static function errorPage(int $code = 403): void
    {
        http_response_code($code);
        header("Location: getError/$code");
    }

    public static function redirectToPreviousPage(): void
    {
        http_response_code(302);
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    /**
     * @return void
     * @desc Redirect to the website home page with 302
     */
    public static function redirectToHome(): void
    {
        http_response_code(302);
        header("Location: " . Utils::getEnv()->getValue("PATH_SUBFOLDER"));
    }

    public static function emulateRoute(string $url): void
    {
        $route = self::getRouteByUrl($url);

        if (is_null($route)) {
            return;
        }

        $route->call();
    }

    /**
     * @return void
     * @desc Redirect browser to previous page
     */
    public static function redirectPreviousRoute(): void
    {
        http_response_code(302);
        header("Location: " .  $_SERVER['HTTP_REFERER']);
    }

}