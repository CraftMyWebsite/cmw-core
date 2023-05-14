<?php

namespace CMW\Utils;


use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Router\Route;
use CMW\Manager\Router\Router;

class Redirect
{

    private static function getRouteByUrl(string $url): ?Route
    {
        $router = Router::getInstance();
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

        $strParams = implode(", ", $params);

        http_response_code(302);
        header("Location: " .  EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . $route->getUrl() . '/' . $strParams);
    }

    /**
     * @param string $url Url or Route Name.
     * @desc Redirect to admin pages and check if the use has admin dashboard perm
     */
    public static function redirectToAdmin(string $url, array $params = []): void
    {
        $route = self::getRouteByUrl("cmw-admin/$url");

        if (is_null($route)) {
            return;
        }

        if (!UsersController::isAdminLogged()){
            self::redirectToHome();
            return;
        }

        $strParams = implode(", ", $params);

        http_response_code(302);
        header("Location: " .  EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . $route->getUrl() . '/' . $strParams);
    }

    /**
     * @param int $code
     * @return void
     * @desc Redirect to errorPage
     */
    public static function errorPage(int $code = 403): void
    {
        http_response_code($code);
        // self::redirect("getError/$code"); ??
        header("Location: getError/$code");
    }

    /**
     * @return void
     * @deprecated please prefer {@see CMW\Utils\Redirect::redirectPreviousRoute()}
     */
    public static function redirectToPreviousPage(): void
    {
        http_response_code(302);
        // use self::redirect ??
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

    /**
     * @return void
     * @desc Redirect to the website home page with 302
     */
    public static function redirectToHome(): void
    {
        http_response_code(302);
        // use self::redirect ??
        header("Location: " . EnvManager::getInstance()->getValue("PATH_SUBFOLDER"));
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
        // use self::redirect ??
        header("Location: " .  $_SERVER['HTTP_REFERER']);
    }

}