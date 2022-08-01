<?php

namespace CMW\Utils;

use CMW\Manager\Class\ClassManager;

use CMW\Controller\Installer\InstallerController;

use CMW\Manager\Error\ErrorManager;
use CMW\Router\Link;
use CMW\Router\Router;
use CMW\Router\RouterException;

use ReflectionClass;

class Loader
{

    private static Router $_routerInstance;
    private static array $fileLoaded = array();

    public function __construct()
    {
        new Utils(); //Need to be first /!\ IMPORTANT
    }

    private static function getValue(string $value): string
    {
        return Utils::getEnv()->getValue($value);
    }

    private function requireFile($directory, ...$files): void
    {
        foreach ($files as $file) {
            require_once(self::getValue("dir") . "$directory/$file");
        }
    }

    private static function callPackage(array $classPart, string $startDir, string $folderPackage = ""): bool
    {

        if (count($classPart) < 4) {
            return false;
        }

        $classPart = array_slice($classPart, 2);
        $packageName = strtolower($classPart[0]);
        $classPart = array_slice($classPart, 1);

        $fileName = array_pop($classPart) . ".php";

        $subFolderFile = count($classPart) ? implode("/", $classPart) . "/" : "";

        $file = self::getValue("dir") . $startDir . $packageName . $folderPackage . $subFolderFile . $fileName;

        if (is_file($file)) {
            require_once($file);
            return true;
        }

        return false;
    }

    private static function callCoreClass(array $classPart, string $startDir): bool
    {

        if (count($classPart) < 3) {
            return false;
        }

        $classPart = array_slice($classPart, 2);

        $fileName = array_pop($classPart) . ".php";

        $subFolderFile = count($classPart) ? implode("/", $classPart) . "/" : "";

        $file = self::getValue("dir") . $startDir . $subFolderFile . $fileName;

        if (is_file($file)) {
            require_once($file);
            return true;
        }

        return false;
    }


    public function setLocale(): void
    {
        Utils::getEnv()->addValue("locale", "fr");
        date_default_timezone_set(Utils::getEnv()->getValue("TIMEZONE") ?? "UTC");
    }

    public function manageErrors(): void
    {
        $devMode = (bool)self::getValue("devMode");
        ini_set('display_errors', $devMode);
        ini_set('display_startup_errors', $devMode);
        error_reporting(E_ALL);
    }


    public static function loadProject(): void
    {
        spl_autoload_register(static function ($class) {


            $classPart = explode("\\", $class);

            if (count($classPart) < 2 || $classPart[0] !== "CMW") {
                return false;
            }

            return match (ucfirst($classPart[1])) {
                "Controller" => Loader::callPackage($classPart, "app/package/", "/controllers/", true),
                "Model" => Loader::callPackage($classPart, "app/package/", "/models/"),
                "Entity" => Loader::callPackage($classPart, "app/package/", "/entities/"),
                "PackageInfo" => Loader::callPackage($classPart, "app/package", "/"),
                "Manager" => Loader::callPackage($classPart, "app/manager/", "/"),
                "Utils" => Loader::callCoreClass($classPart, "app/tools/"),
                "Router" => Loader::callCoreClass($classPart, "router/"),
                default => false,
            };
        });
    }

    public function loadLangFiles(): void
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


    public function getRouterInstance($url = ""): Router
    {
        if (!isset(self::$_routerInstance)) {
            self::$_routerInstance = new Router($_GET['url'] ?? $url);
        }

        return self::$_routerInstance;
    }

    public function listenRouter(): void
    {
        $router = self::$_routerInstance;

        try {
            $router->listen();
        } catch (RouterException $e) {
            ErrorManager::showError($e->getCode());
            return;
        }
    }

    public function loadRoutes(): void
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
                        self::initRoute($file);
                    }
                }
            }
        }
    }

    /**
     * @throws \ReflectionException
     */
    private static function initRoute(string $file): void
    {
        if (in_array($file, self::$fileLoaded, true)) {
            return;
        }

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

                self::$_routerInstance->registerRoute($linkInstance, $method);
            }

        }

        self::$fileLoaded[] = $file;

    }


    public function installManager(): void
    {
        $this->requireFile("installation", "routes.php", "controllers/InstallerController.php", "models/InstallerModel.php"); //Todo See that
        if (is_dir("installation")) {
            if ((int)self::getValue("installStep") >= 0) {

                InstallerController::goToInstall();

            } elseif (!self::getValue("devMode")) {
                Utils::deleteDirectory("installation");
            }
        }
    }

}