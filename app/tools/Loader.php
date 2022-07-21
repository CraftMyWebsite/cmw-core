<?php

namespace CMW\Utils;

use CMW\Controller\Installer\InstallerController;
use CMW\Router\Link;
use CMW\Router\Router;
use CMW\Router\RouterException;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

class Loader
{

    private static Router $globalRouter;

    public function __construct()
    {
        new Utils(); //Need to be first /!\ IMPORTANT
        $this->loadRouter();
    }

    private function getValue(string $value): string
    {
        return Utils::getEnv()->getValue($value);
    }

    public function setLocale(): void
    {
        Utils::getEnv()->addValue("locale", "fr");
        date_default_timezone_set(Utils::getEnv()->getValue("TIMEZONE") ?? "UTC");
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

            $this->requireFile("router", "Link.php");
        }

        return self::$globalRouter;
    }

    public static function getRouter(): Router
    {
        return self::$globalRouter;
    }

    /**
     * @throws \ReflectionException
     */
    public function loadPackages(): void
    {
        $this->requireFile("app", "Manager.php");

        $this->loadLangFiles();
        $this->loadControllers();
        $this->loadMultiplePackageFiles("entities", "functions", "models");
        $this->loadRouteFiles();

        if ((int)$this->getValue("installStep") >= 0) {
            $this->requireFile("installation", "routes.php", "controllers/InstallerController.php", "models/InstallerModel.php");
        }
    }

    public function loadTools(): void
    {
        $this->requireFile("app/tools", "View.php", "ErrorManager.php", "ClassManager.php");
    }

    public function loadGlobalConstants(): void
    {
        $this->requireFile("app/tools", "functions.php");
        $this->requireFile("app", "globalConst.php");
    }

    public function listenRouter(): void
    {
        $router = self::$globalRouter;

        try {
            $router->listen();
        } catch (RouterException $e) {
            ErrorManager::redirectError($e->getCode());
            return;
        } catch (Throwable $e) {
            echo "Erreur $e";
        }
    }

    public function installManager(): void
    {
        if (is_dir("installation")) {
            if ((int)$this->getValue("installStep") >= 0) {

                InstallerController::goToInstall();

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

    private function loadLangFiles(): void
    {
        $packageFolder = 'app/package';
        $contentDirectory = array_diff(scandir("$packageFolder/"), array('..', '.'));
        $dir = Utils::getEnv()->getValue("dir");

        foreach ($contentDirectory as $package) {
            $packageSubFolder = "$packageFolder/$package/lang";
            if (is_dir($packageSubFolder)) {
                $contentSubDirectory = array_diff(scandir("$packageSubFolder/"), array('..', '.'));
                foreach ($contentSubDirectory as $packageFile) {
                    $file = "$dir$packageSubFolder/" . getenv("LOCALE") . ".php";
                    if (is_file($file)) {
                        require_once($file);
                    }
                }
            }
        }
    }

    private function loadRouteFiles(): void
    {
        $packageFolder = 'app/package';
        $scannedDirectory = array_diff(scandir("$packageFolder/"), array('..', '.'));
        $dir = Utils::getEnv()->getValue("dir");

        foreach ($scannedDirectory as $package) {
            $file = "$dir$packageFolder/$package/routes.php";
            if (is_file($file)) {
                require($file);
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    private function loadControllers(): void
    {
        $packageFolder = 'app/package';
        $contentDirectory = array_diff(scandir("$packageFolder/"), array('..', '.'));
        $dir = Utils::getEnv()->getValue("dir");

        foreach ($contentDirectory as $package) {
            $packageSubFolder = "$packageFolder/$package/controllers";
            if (is_dir($packageSubFolder)) {
                $contentSubDirectory = array_diff(scandir("$packageSubFolder/"), array('..', '.'));
                foreach ($contentSubDirectory as $packageFile) {
                    $file = "$dir$packageSubFolder/$packageFile";
                    if (is_file($file)) {
                        require_once($file);
                        $this->initRoute($file);
                    }
                }
            }
        }

    }

    /**
     * @throws \ReflectionException
     */
    private function initRoute(string $file): void
    {
        $className = ClassManager::getClassFullNameFromFile($file);

        $classRef = new ReflectionClass($className);
        foreach ($classRef->getMethods() as $method) {

            $isMethodClass = $method->getDeclaringClass()->getName() === $className;

            if (!$isMethodClass) {
                continue;
            }

            $linkAttributes = $method->getAttributes(Link::class);
            foreach ($linkAttributes as $attribute) {

                /** @var Link $linkInstance */
                $linkInstance = $attribute->newInstance();

                self::$globalRouter->registerRoute($linkInstance, $method);
            }

        }

    }

}