<?php

namespace CMW\Utils;

use CMW\Controller\Installer\InstallerController;
use CMW\Router\Router;
use CMW\Router\RouterException;

class Loader
{

    private Utils $utils;
    private static Router $globalRouter;

    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
        $this->loadRouter();
    }

    private function getValue(string $value): string
    {
        return $this->utils::getEnv()->getValue($value);
    }

    public function manageErrors(): void
    {
        $devMode = (bool)$this->getValue("devMode");
        ini_set('display_errors', $devMode);
        ini_set('display_startup_errors', $devMode);
        error_reporting(E_ALL);
    }

    private function requireFile($directory, ...$files): void
    {
        foreach ($files as $file) {
            require_once($this->getValue("dir") . "$directory/$file");
        }
    }

    public function loadRouter($url = ""): Router
    {
        if (!isset(self::$globalRouter)) {
            $this->requireFile("router", "Router.php", "Route.php", "RouterException.php");

            $router = new Router($_GET['url'] ?? $url);
            self::$globalRouter = $router;
        }

        return self::$globalRouter;
    }

    public function loadPackages(): void
    {
        $this->requireFile("app", "manager.php");

        $this->loadMultiplePackageFiles("controllers", "entities", "functions", "models");

        $this->requireFile("app/package-loader", "__routes.php", "__lang.php");

        if ((int)$this->getValue("installStep") >= 0) {
            $this->requireFile("installation", "routes.php", "controllers/installerController.php", "models/installerModel.php");
        }
    }

    public function loadGlobalConstants(): void
    {
        $this->requireFile("app/tools", "builder.php", "functions.php");
        $this->requireFile("app", "globalConst.php");
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

    public function installManager(): void
    {
        if (is_dir("installation")) {
            if ((int)$this->getValue("installStep") >= 0) {
                $this->requireFile("installation/controllers", "installerController.php");

                $installation = new InstallerController();

                $installation->goToInstall();
            } elseif (!$this->getValue("devMode")) {
                Utils::deleteDirectory("installation");
            }
        }
    }

    private function loadMultiplePackageFiles(string ...$packages): void
    {
        foreach ($packages as $package) {
            $this->loadPackageFiles($package);
        }
    }

    private function loadPackageFiles(string $partName): void
    {
        $packageFolder = 'app/package';
        $contentDirectory = array_diff(scandir("$packageFolder/"), array('..', '.'));
        $dir = Utils::getEnv()->getValue("dir");

        foreach ($contentDirectory as $package) {
            $packageSubFolder = "$packageFolder/$package/$partName";
            if (is_dir($packageSubFolder)) {
                $contentSubDirectory = array_diff(scandir("$packageSubFolder/"), array('..', '.'));
                foreach ($contentSubDirectory as $packageFile) {
                    $file = "$dir$packageSubFolder/$packageFile";
                    if (is_file($file)) {
                        require_once($file);
                    }
                }
            }
        }
    }

}