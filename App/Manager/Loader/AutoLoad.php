<?php

namespace CMW\Manager\Loader;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\PackageController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Error\ErrorManager;
use CMW\Utils\Website;
use UnhandledMatchError;
use function array_pop;
use function array_slice;
use function count;
use function dirname;
use function explode;
use function get_declared_classes;
use function implode;
use function in_array;
use function ini_set;
use function is_file;
use function session_start;
use function session_status;
use function spl_autoload_register;
use function str_replace;
use function ucfirst;
use const DIRECTORY_SEPARATOR;
use const PHP_SESSION_ACTIVE;

class AutoLoad
{
    public static array $findNameSpace = [];

    private static function isEnvValid(): bool
    {
        require_once('App/Manager/Error/ErrorManager.php');
        require_once('App/Manager/Env/EnvManager.php');
        require_once('App/Manager/Class/PackageManager.php');
        return is_file(EnvManager::getInstance()->getValue('DIR') . 'index.php');
    }

    private static function updateEnv(): void
    {
        EnvManager::getInstance()->setOrEditValue('DIR', dirname(__DIR__, 2) . '/');
        EnvManager::getInstance()->setOrEditValue('PATH_URL', Website::getUrl());
    }

    private static function register(): void
    {
        spl_autoload_register(static function (string $class) {
            $classPart = explode('\\', $class);

            if (in_array($class, get_declared_classes())) {
                return false;
            }

            if ($classPart[0] !== 'CMW') {
                return false;
            }

            if ((count($classPart) >= 4) && $classPart[2] === 'Installer') {
                return match (ucfirst($classPart[1])) {
                    'Controller' => self::callPackage($classPart, 'Installation/', '/Controllers/'),
                    'Model' => self::callPackage($classPart, 'Installation/', '/Models/'),
                };
            }

            return self::getPackageElements($classPart, $classPart[1]);
        });
    }

    /**
     * @return void
     * @desc Load theme router
     */
    private static function loadThemeRouter(): void
    {
        if (EnvManager::getInstance()->getValue('INSTALLSTEP') === '-1') {
            $theme = CoreController::getThemePath();
            if (!$theme) {
                return;
            }

            $routerPath = "$theme/router.php";

            if (!is_file($routerPath)) {
                return;
            }

            require_once $routerPath;
        }
    }

    private static function setupSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.gc_maxlifetime', 600480);  // 7 days
            ini_set('session.cookie_lifetime', 600480);  // 7 days
            session_set_cookie_params(600480, EnvManager::getInstance()->getValue('PATH_SUBFOLDER'), null, false, true);
            session_start();
        }
    }

    private static function getPackageElements(array $namespace, string $elementName): ?string
    {
        $startDir = static function ($elementName) use ($namespace) { //Don't remove use $namespace
            try {
                return match ($elementName) {
                    'Controller', 'Model', 'Mapper', 'Entity', 'Implementation', 'Interface', 'Event', 'Exception', 'Type', 'Component', 'PackageInfo', 'Package', 'Permissions' => 'App/Package/',
                    'Manager' => 'App/Manager/',
                    'Utils' => 'App/Utils/',
                    'Theme' => 'Public/Themes/',
                    default => '',
                };
            } catch (UnhandledMatchError $e) {
                $nameSpaceImploded = implode('\\', $namespace);

                ErrorManager::showCustomErrorPage(
                    "UnhandledMatchError",
                    "Unable to load <b>$elementName</b> element. Full namespace: <b>$nameSpaceImploded</b>",
                );
            }
        };

        $folderPackage = static function ($elementName) use ($namespace) { //Don't remove use $namespace
            try {
                return match ($elementName) {
                    'Controller' => 'Controllers/',
                    'Model' => 'Models/',
                    'Mapper' => 'Mappers/',
                    'Entity' => 'Entities/',
                    'Implementation' => 'Implementations/',
                    'Interface' => 'Interfaces/',
                    'Event' => 'Events/',
                    'Exception' => 'Exception/',
                    'Type' => 'Type/',
                    'Component' => 'Components/',
                    'PackageInfo', 'Manager' => '',
                    'Package', 'Theme' => '/',
                    'Permissions' => 'Init/',
                };
            } catch (UnhandledMatchError $e) {
                $nameSpaceImploded = implode('\\', $namespace);

                ErrorManager::showCustomErrorPage(
                    "UnhandledMatchError",
                    "Unable to load <b>$elementName</b> element. Full namespace: <b>$nameSpaceImploded</b>",
                );
            }
        };

        return match ($elementName) {
            'Utils' => self::callCoreClass($namespace, $startDir($elementName)),
            'Implementation' => self::callPackageImplementations($namespace, $startDir($elementName), "/{$folderPackage($elementName)}"),
            default => self::callPackage($namespace, $startDir($elementName), "/{$folderPackage($elementName)}")
        };
    }

    private static function callPackage(array $classPart, string $startDir, string $folderPackage = ''): bool
    {
        if (empty($startDir) || count($classPart) < 4) {
            return false;
        }

        $namespace = implode('\\', $classPart);
        $packageName = $classPart[2];
        $fileName = $classPart[count($classPart) - 1] . '.php';

        $subFolderFile = '';
        if (count($classPart) > 4) {
            $subFolderFile = implode('\\', array_slice($classPart, 3, -1)) . '\\';
        }

        $dir = EnvManager::getInstance()->getValue('DIR');
        $filePath = $dir . $startDir . ($packageName === 'Installer' ? '' : $packageName) . $folderPackage . $subFolderFile . $fileName;

        $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $filePath);

        if (!is_file($filePath)) {
            return false;
        }

        self::$findNameSpace[str_replace('/', '\\', $filePath)] = $namespace;

        require_once $filePath;
        return true;
    }

    private static function callPackageImplementations(array $classPart, string $startDir, string $folderPackage = ''): bool
    {
        if (empty($startDir) || count($classPart) !== 5) {
            return false;
        }

        $namespace = implode('\\', $classPart);
        $packageName = $classPart[2];
        $fileName = $classPart[count($classPart) - 1] . '.php';

        if (!PackageController::isInstalled($classPart[3])) {
            return false;
        }

        $subFolderFile = '';
        if (count($classPart) > 4) {
            $subFolderFile = implode('\\', array_slice($classPart, 3, -1)) . '\\';
        }

        $dir = EnvManager::getInstance()->getValue('DIR');
        $filePath = $dir . $startDir . $packageName . $folderPackage . $subFolderFile . $fileName;

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

        $fileName = array_pop($classPart) . '.php';

        $subFolderFile = count($classPart) ? implode('/', $classPart) . '/' : '';

        $filePath = EnvManager::getInstance()->getValue('DIR') . $startDir . $subFolderFile . $fileName;

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

        self::loadThemeRouter();

        self::setupSession();
    }
}
