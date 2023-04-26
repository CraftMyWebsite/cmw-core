<?php

namespace CMW\Utils;

use CMW\Controller\Core\CoreController;
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
    private static array $fileLoadedAttr = array();
    private static array $attributeList = array();

    public function __construct()
    {
        require_once("app/tools/Utils.php");
        new Utils(); //Need to call Utils __construct.
    }

    private static function getValue(string $value): string
    {
        return Utils::getEnv()->getValue($value);
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

        $file = self::getValue("dir") . $startDir . ($packageName === "installer" ? "" : $packageName) . $folderPackage . $subFolderFile . $fileName;

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

    public static function getAttributeList(): array
    {
        return self::$attributeList;
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

            if ((count($classPart) >= 4) && $classPart[2] === "Installer") {
                return Loader::callPackage($classPart, "installation", "/controllers/");
            }

            return match (ucfirst($classPart[1])) {
                "Controller" => Loader::callPackage($classPart, "app/package/", "/controllers/", true),
                "Model" => Loader::callPackage($classPart, "app/package/", "/models/"),
                "Entity" => Loader::callPackage($classPart, "app/package/", "/entities/"),
                "PackageInfo" => Loader::callPackage($classPart, "app/package", "/"),
                "Manager" => Loader::callPackage($classPart, "app/manager/", "/"),
                "Utils" => Loader::callCoreClass($classPart, "app/tools/"),
                "Router" => Loader::callCoreClass($classPart, "router/"),
                "Installer" => Loader::callCoreClass($classPart, "installer"),
            };

        });

        //Load router files in front-end

        //Load attributes


        if (Utils::getEnv()->getValue("INSTALLSTEP") === '-1') {
            new CoreController();
            $theme = CoreController::getThemePath();
            if ($theme) {

                $viewsPath = "$theme/views/";
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

    public function listenRouter(): void
    {

        try {
            Router::getInstance()->listen();
        } catch (RouterException $e) {
            ErrorManager::showError($e->getCode());
            return;
        }
    }

    public function loadRoutes($linkClass = Link::class): void
    {
        $attrList = self::$attributeList[$linkClass];

        if (!isset($attrList)) {
            return;
        }

        foreach ($attrList as [$attr, $method]) {
            $linkInstance = $attr->newInstance();
            Router::getInstance()->registerRoute($linkInstance, $method);
        }

    }

    /**
     * @throws \ReflectionException
     */
    public function loadAttributes(): void
    {
        $files = array_merge(
            Utils::getFilesFromDirectory("app/package", "php"),
            Utils::getFilesFromDirectory("installation", "php")
        );

        foreach ($files as $file) {
            self::listAttributes($file);
        }
    }

    public static function createSimpleRoute(string $path, string $fileName, string $package, ?string $name = null, int $weight = 2): void
    {
        Router::getInstance()->get($path, function () use ($package, $fileName) {
            View::basicPublicView($package, $fileName);
        }, $name, $weight);
    }

    /**
     * @throws \ReflectionException
     */
    public static function listAttributes($file): void
    {
        if (in_array($file, self::$fileLoadedAttr, true)) {
            return;
        }

        $className = ClassManager::getClassFullNameFromFile($file);

        if (is_null($className)) {
            return;
        }

        $classRef = new ReflectionClass($className);
        foreach ($classRef->getMethods() as $method) {

            $isMethodClass = $method->getDeclaringClass()->getName() === $className;

            if (!$isMethodClass) {
                continue;
            }

            $attrList = $method->getAttributes();
            foreach ($attrList as $attribute) {


                if (!isset(self::$attributeList[$attribute->getName()])) {
                    self::$attributeList[$attribute->getName()] = array();
                }

                self::$attributeList[$attribute->getName()][] = [$attribute, $method];
            }
        }

        self::$fileLoadedAttr[] = $file;
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
        if (is_dir("installation")) {
            if (Utils::getEnv()->getValue("INSTALLSTEP") !== '-1') {

                ErrorManager::enableErrorDisplays(true);

                InstallerController::goToInstall();
            }
        } elseif (!Utils::getEnv()->getValue("DEVMODE")) {
            Utils::deleteDirectory("installation");
        }
    }

}
