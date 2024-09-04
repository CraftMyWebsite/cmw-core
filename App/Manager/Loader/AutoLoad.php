<?php

namespace CMW\Manager\Loader;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\PackageController;
use CMW\Manager\Env\EnvManager;
use CMW\Utils\Directory;
use CMW\Utils\Website;

class AutoLoad
{

    public static array $findNameSpace = [];

    private static function isEnvValid(): bool
    {
        require_once("App/Manager/Error/ErrorManager.php");
        require_once("App/Manager/Env/EnvManager.php");
        require_once("App/Manager/Class/PackageManager.php");
        return is_file(EnvManager::getInstance()->getValue("DIR") . "index.php");
    }

    private static function updateEnv(): void
    {
        EnvManager::getInstance()->setOrEditValue("DIR", dirname(__DIR__, 2) . "/");
        EnvManager::getInstance()->setOrEditValue("PATH_URL", Website::getUrl());
    }

    private static function register(): void
    {
        spl_autoload_register(static function (string $class) {

            $classPart = explode("\\", $class);

            if (in_array($class, get_declared_classes())) {
                return false;
            }

            if ($classPart[0] !== "CMW") {
                return false;
            }

            if ((count($classPart) >= 4) && $classPart[2] === "Installer") {
                return match (ucfirst($classPart[1])) {
                    "Controller" => self::callPackage($classPart, "Installation/", "/Controllers/"),
                    "Model" => self::callPackage($classPart, "Installation/", "/Models/"),
                };
            }

            return self::getPackageElements($classPart, $classPart[1]);
        });
    }

    private static function loadThemeRoutes(): void
    {
        if (EnvManager::getInstance()->getValue("INSTALLSTEP") === '-1') {
            $theme = CoreController::getThemePath();
            if ($theme) {

                $viewsPath = "$theme/Views/";
                $dirList = Directory::getFolders($viewsPath);

                foreach ($dirList as $package) {
                    $packagePath = $viewsPath . $package . "/";

                    $packageDir = Directory::getFiles($packagePath);

                    foreach ($packageDir as $file) {
                        $packageFile = $packagePath . $file;
                        if ($file === "router.php" && is_file($packageFile)) {
                            require_once($packageFile);
                        }
                    }

                }

            }
        }
    }

    private static function setupSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.gc_maxlifetime', 600480); // 7 days
            ini_set('session.cookie_lifetime', 600480); // 7 days
            session_set_cookie_params(600480, EnvManager::getInstance()->getValue("PATH_SUBFOLDER"), null, false, true);
            session_start();
        }
    }

    private static function getPackageElements(array $namespace, string $elementName): ?string
    {
        $startDir = static function ($elementName) {
            return match ($elementName) {
                "Controller", "Model", "Entity", "Implementation", "Interface", "Event", "Exception", "Type", "PackageInfo", "Package", "Permissions" => "App/Package/",
                "Manager" => "App/Manager/",
                "Utils" => "App/Utils/",
                "Theme" => "Public/Themes/",
                default => "",
            };
        };

        $folderPackage = static function ($elementName) {
            return match ($elementName) {
                "Controller" => "Controllers/",
                "Model" => "Models/",
                "Entity" => "Entities/",
                "Implementation" => "Implementations/",
                "Interface" => "Interfaces/",
                "Event" => "Events/",
                "Exception" => "Exception/",
                "Type" => "Type/",
                "PackageInfo", "Manager" => "",
                "Package", "Theme" => "/",
                "Permissions" => "Init/",
            };
        };

        return match ($elementName) {
            "Utils" => self::callCoreClass($namespace, $startDir($elementName)),
            "Implementation" => self::callPackageImplementations($namespace, $startDir($elementName), "/{$folderPackage($elementName)}"),
            default => self::callPackage($namespace, $startDir($elementName), "/{$folderPackage($elementName)}")
        };
    }


    private static function callPackage(array $classPart, string $startDir, string $folderPackage = ""): bool
    {
        if (empty($startDir) || count($classPart) < 4) {
            return false;
        }

        $namespace = implode('\\', $classPart);
        $packageName = strtolower($classPart[2]);
        $fileName = $classPart[count($classPart) - 1] . ".php";

        $subFolderFile = '';
        if (count($classPart) > 4) {
            $subFolderFile = implode('\\', array_slice($classPart, 3, -1)) . '\\';
        }

        $dir = EnvManager::getInstance()->getValue("DIR");
        $filePath = $dir . $startDir . ($packageName === "installer" ? "" : ucfirst($packageName)) . $folderPackage . $subFolderFile . $fileName;

        $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $filePath);

        if (!is_file($filePath)) {
            return false;
        }

        self::$findNameSpace[str_replace('/', '\\', $filePath)] = $namespace;

        require_once $filePath;
        return true;
    }

    private static function callPackageImplementations(array $classPart, string $startDir, string $folderPackage = ""): bool
    {
        if (empty($startDir) || count($classPart) !== 5) {
            return false;
        }

        $namespace = implode('\\', $classPart);
        $packageName = strtolower($classPart[2]);
        $fileName = $classPart[count($classPart) - 1] . ".php";

        if (!PackageController::isInstalled($classPart[3])) {
            return false;
        }

        $subFolderFile = '';
        if (count($classPart) > 4) {
            $subFolderFile = implode('\\', array_slice($classPart, 3, -1)) . '\\';
        }

        $dir = EnvManager::getInstance()->getValue("DIR");
        $filePath = $dir . $startDir . ucfirst($packageName) . $folderPackage . $subFolderFile . $fileName;

        $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $filePath);

        if (!is_file($filePath)) {
            return false;
        }

        self::$findNameSpace[str_replace('/', '\\', $filePath)] = $namespace;

        require_once $filePath;
        return true;
    }

    private static function callCoreClass(array $classPart, string $startDir): bool
    {

        if (count($classPart) < 3) {
            return false;
        }

        $namespace = implode('\\', $classPart);

        $classPart = array_slice($classPart, 2);

        $fileName = array_pop($classPart) . ".php";

        $subFolderFile = count($classPart) ? implode("/", $classPart) . "/" : "";

        $filePath = EnvManager::getInstance()->getValue("DIR") . $startDir . $subFolderFile . $fileName;

        if (!is_file($filePath)) {
            return false;
        }

        self::$findNameSpace[str_replace('/', '\\', $filePath)] = $namespace;

        require_once($filePath);
        return true;
    }

    public static function load(): void
    {
        if (!self::isEnvValid()) {
            self::updateEnv();
        }

        self::register();

        self::loadThemeRoutes();

        self::setupSession();
    }

}