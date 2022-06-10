<?php

namespace CMW\Utils;

use CMW\Controller\Installer\installerController;
use CMW\Router\router;
use CMW\Router\routerException;

require("EnvBuilder.php");

class Utils
{
    private static EnvBuilder $env;
    private static Router $globalRouter;

    public function __construct()
    {
        self::$env ??= new EnvBuilder();
        $this->loadRouter();
    }

    public static function getEnv(): EnvBuilder
    {
        return self::$env;
    }

    public function loadRouter($url = ""): Router
    {
        if (!isset(self::$globalRouter)) {
            require_once(self::getEnv()->getValue("dir") . "router/router.php");
            require_once(self::getEnv()->getValue("dir") . "router/route.php");
            require_once(self::getEnv()->getValue("dir") . "router/routerException.php");

            $router = new Router($_GET['url'] ?? $url);
            self::$globalRouter = $router;
        }

        return self::$globalRouter;
    }

    public function loadPackages(): void
    {
        require_once(self::getEnv()->getValue("dir") . "app/manager.php");
        require_once(self::getEnv()->getValue("dir") . "app/__model.php");
        require_once(self::getEnv()->getValue("dir") . "app/__controller.php");
        require_once(self::getEnv()->getValue("dir") . "app/__routes.php");
        if ((int)self::getEnv()->getValue("installStep") >= 0) {
            require_once(self::getEnv()->getValue("dir") . "installation/routes.php");
            require_once(self::getEnv()->getValue("dir") . "installation/controllers/installerController.php");
            require_once(self::getEnv()->getValue("dir") . "installation/models/installerModel.php");
        }
    }

    public function loadGlobalConstants(): void
    {
        require_once(self::getEnv()->getValue("dir") . "app/tools/builder.php");
        require_once(self::getEnv()->getValue("dir") . "app/tools/functions.php");
        require_once(self::getEnv()->getValue("dir") . "app/globalConst.php");
    }

    public function listenRouter(): void
    {
        $router = self::$globalRouter;

        try {
            $router->listen();
        } catch (RouterException $e) {
            exit($e->getMessage());
        }
    }

    public static function isValuesEmpty(array $array, string ...$values): bool
    {
        foreach ($values as $value) {
            if (empty($array[$value])) {
                return true;
            }
        }

        return false;
    }

    public function installManager(): void
    {
        if (is_dir("installation")) {
            if ((int)self::getEnv()->getValue("installStep") >= 0) {
                require_once(self::getEnv()->getValue("dir") . "installation/controllers/installerController.php");

                $installation = new installerController();
                //$Installator->isInstalled() ? $Installator->deleteInstallation() :
                $installation->goToInstall();
            } elseif (!self::getEnv()->getValue("devMode")) {
                deleteDirectory("installation");
            }
        }
    }

    public function manageErrors(): void
    {
        $devMode = (bool)self::getEnv()->getValue("devMode");
        ini_set('display_errors', $devMode);
        ini_set('display_startup_errors', $devMode);
        error_reporting(E_ALL);
    }

}