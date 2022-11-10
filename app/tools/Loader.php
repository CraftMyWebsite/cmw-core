<?php

namespace CMW\Utils;

use CMW\Controller\Core\CoreController;
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
        require_once("app/tools/Utils.php");
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
        date_default_timezone_set(Utils::getEnv()->getValue("TIMEZONE") ?? "Europe/Paris");
    }

    public function manageErrors(string $em): void
    {
        $class = new $em;
        $class();
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

        //Load router files in front-end
        new CoreController();
        $theme = CoreController::getThemePath();
        if ($theme) {

            $viewsPath = "./public/themes/$theme/views/";
            $dirList = array_diff(scandir($viewsPath), array('.', '..'));

            foreach ($dirList as $package) {
                $packagePath = $viewsPath . $package . "/";

                if (is_dir($viewsPath . $package)) {

                    $packageDir = array_diff(scandir($packagePath), array('.', '..'));

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

        $package = strtolower($package);

        $fileName = "app/package/$package/lang/$lang.php";
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

    public static function createSimpleRoute(string $path, string $fileName, string $package, ?string $name = null, int $weight = 1): void
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


    public function installManager(): void
    {
        $this->requireFile("installation", "controllers/InstallerController.php", "models/InstallerModel.php"); //Todo See that

        self::initRoute(self::getValue("dir") . "installation/controllers/InstallerController.php");
        if (is_dir("installation")) {
            if ((int)self::getValue("installStep") >= 0) {

                InstallerController::goToInstall();

            } elseif (!self::getValue("devMode")) {
                Utils::deleteDirectory("installation");
            }
        }
    }

}
