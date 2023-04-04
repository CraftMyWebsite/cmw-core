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
    public static function redirect(string $url): void
    {
        $route = self::getRouteByUrl($url);

        if (is_null($route)) {
            return;
        }

        http_response_code(302);
        header("Location: " . getenv("PATH_SUBFOLDER") . $route->getUrl());
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