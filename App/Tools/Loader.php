<?php

namespace CMW\Utils;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\PackageController;
use CMW\Manager\Class\ClassManager;

use CMW\Controller\Installer\InstallerController;

use CMW\Manager\Error\ErrorManager;
use CMW\Manager\Views\View;
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
        require_once("App/Tools/Utils.php");
        new Utils(); //Need to call Utils __construct.
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
        $packageName = ucfirst($classPart[0]);
        $classPart = array_slice($classPart, 1);

        $fileName = array_pop($classPart) . ".php";

        $subFolderFile = count($classPart) ? implode("/", $classPart) . "/" : "";

        $file = self::getValue("DIR") . $startDir . $packageName . $folderPackage . $subFolderFile . $fileName;

        if (is_file($file)) {
            require_once($file);
            return true;
        }

        return false;
    }

    public static function loadImplementations(string $interface): array
    {
        $toReturn = [];

        $packages = PackageController::getInstalledPackages();

        foreach ($packages as $package) {
            $implementationsFolder = Utils::getEnv()->getValue("dir") . "App/Package/{$package->getName()}/Implementations";

            if (!is_dir($implementationsFolder)) {
                continue;
            }

            $implementationsFiles = array_diff(scandir($implementationsFolder), array('..', '.'));

            foreach ($implementationsFiles as $implementationsFile) {

                $implementationsFilePath = Utils::getEnv()->getValue("dir") . "App/Package/" .
                    ucfirst($package->getName()) . "/Implementations/" .$implementationsFile;

                $className = pathinfo($implementationsFilePath, PATHINFO_FILENAME);

                $namespace = 'CMW\\Implementation\\' . ucfirst($package->getName()) . '\\' . $className;

                $toReturn[] = new $namespace();
            }
        }

        return $toReturn;
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
        date_default_timezone_set(Utils::getEnv()->getValue("TIMEZONE") ?? "Europe/Paris");
    }

    public function manageErrors(string $em): void
    {
        $class = new $em;
        $class();
    }

    public static function loadProject(): void
    {

        if (!(new Loader)->isEnvValid()) {
            (new Loader)->updateEnv();
        }

        spl_autoload_register(static function ($class) {


            $classPart = explode("\\", $class);

            if (count($classPart) < 2 || $classPart[0] !== "CMW") {
                return false;
            }

            return match (ucfirst($classPart[1])) {
                "Controller" => Loader::callPackage($classPart, "App/Package/", "/Controllers/"),
                "Model" => Loader::callPackage($classPart, "App/Package/", "/Models/"),
                "Entity" => Loader::callPackage($classPart, "App/Package/", "/Entities/"),
                "Interface" => Loader::callPackage($classPart, "App/Package/", "/Interfaces/"),
                "Implementation" => Loader::callPackage($classPart, "App/Package/", "/Implementations/"),
                "PackageInfo" => Loader::callPackage($classPart, "App/Package", "/"),
                "Manager" => Loader::callPackage($classPart, "App/Manager/", "/"),
                "Utils" => Loader::callCoreClass($classPart, "App/Tools/"),
                "Router" => Loader::callCoreClass($classPart, "Router/"),
                default => false,
            };
        });

        //Load router files in front-end

        if (Utils::getEnv()->getValue("INSTALLSTEP") === '-1') {
            new CoreController();
            $theme = CoreController::getThemePath();
            if ($theme) {

                $viewsPath = "$theme/Views/";
                $dirList = Utils::getFoldersInFolder($viewsPath);

                foreach ($dirList as $package) {
                    $packagePath = $viewsPath . $package . "/";

                    $packageDir = Utils::getFilesInFolder($packagePath);

                    foreach ($packageDir as $file) {
                        $packageFile = $packagePath . $file;
                        if ($file === "router.php" && is_file($packageFile)) {
                            require_once($packageFile);
                        }
                    }

                }

            }
        }


        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.gc_maxlifetime', 600480); // 7 days
            ini_set('session.cookie_lifetime', 600480); // 7 days
            session_set_cookie_params(600480, Utils::getEnv()->getValue("PATH_SUBFOLDER"), null, false, true);
            session_start();
        }

    }


    public static function loadLang(string $package, ?string $lang): ?array
    {

        $package = ucfirst($package);

        $fileName = "App/Package/$package/Lang/$lang.php";
        $fileExist = is_file($fileName);

        if (!$fileExist) {
            return null;
        }

        $fileContent = include $fileName;

        if (!is_array($fileContent)) {
            return null;
        }

        return $fileContent;
    }


    public static function getRouterInstance($url = ""): Router
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
        $packageFolder = 'App/Package';
        $contentDirectory = array_diff(scandir("$packageFolder/"), array('..', '.'));
        $dir = Utils::getEnv()->getValue("DIR");
        foreach ($contentDirectory as $package) {
            $packageSubFolder = "$packageFolder/$package/Controllers";
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

    public static function createSimpleRoute(string $path, string $fileName, string $package, ?string $name = null, int $weight = 2): void
    {
        self::getRouterInstance()->get($path, function () use ($package, $fileName) {
            View::basicPublicView($package, $fileName);
        }, $name, $weight);
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

    /**
     * @return bool
     * @desc Check if the current env config is valid
     */
    private function isEnvValid(): bool
    {
        return is_file(Utils::getEnv()->getValue("DIR") . "index.php");
    }

    /**
     * @return void
     * @desc Update DIR & PATH_URL ENV values
     */
    private function updateEnv(): void
    {
        Utils::getEnv()->setOrEditValue("DIR", dirname(__DIR__, 2) . "/");
        Utils::getEnv()->setOrEditValue("PATH_URL", Utils::getCompleteUrl());
    }

    public function installManager(): void
    {
        if (is_dir("Installation")) {
            if (Utils::getEnv()->getValue("INSTALLSTEP") !== '-1') {

                ErrorManager::enableErrorDisplays(true);

                $this->requireFile("Installation", "Controllers/InstallerController.php", "Models/InstallerModel.php"); //Todo See that
                self::initRoute(self::getValue("dir") . "Installation/Controllers/InstallerController.php");

                InstallerController::goToInstall();
            }
        } elseif (!Utils::getEnv()->getValue("DEVMODE")) {
            Utils::deleteDirectory("Installation");
        }
    }

}
