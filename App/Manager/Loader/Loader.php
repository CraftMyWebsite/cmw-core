<?php

namespace CMW\Manager\Loader;

use CMW\Controller\Core\PackageController;
use CMW\Controller\Installer\InstallerController;
use CMW\Manager\Class\PackageManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Error\ErrorManager;
use CMW\Manager\Router\Link;
use CMW\Manager\Router\Router;
use CMW\Manager\Router\RouterException;
use CMW\Manager\Views\View;
use CMW\Utils\Directory;
use ReflectionClass;

class Loader
{
    private static array $fileLoadedAttr = [];
    private static array $attributeList = [];

    public static function loadProject(): void
    {
        require_once("AutoLoad.php");
        AutoLoad::load();
    }

    private static function &getAttributeListPointer(): array
    {
        return self::$attributeList;
    }

    public static function getAttributeList(): array
    {
        return self::$attributeList;
    }

    public static function loadImplementations(string $interface): array
    {
        $toReturn = [];

        $packages = PackageController::getAllPackages();

        foreach ($packages as $package) {
            $implementationsFolder = EnvManager::getInstance()->getValue("dir") . "App/Package/{$package->name()}/Implementations";

            if (!is_dir($implementationsFolder)) {
                continue;
            }

            $implementationsFiles = array_diff(scandir($implementationsFolder), ['..', '.']);

            foreach ($implementationsFiles as $implementationsFile) {

                $implementationsFilePath = EnvManager::getInstance()->getValue("dir") . "App/Package/" .
                    $package->name() . "/Implementations/" . $implementationsFile;

                $className = pathinfo($implementationsFilePath, PATHINFO_FILENAME);

                $namespace = 'CMW\\Implementation\\' . $package->name() . '\\' . $className;

                $classInstance = new $namespace();

                if (!is_subclass_of($classInstance, $interface)) {
                    continue;
                }

                $toReturn[] = $classInstance;
            }
        }

        return $toReturn;
    }

    public static function setLocale(): void
    {
        EnvManager::getInstance()->addValue("locale", "fr"); //Why fr ?
        date_default_timezone_set(EnvManager::getInstance()->getValue("TIMEZONE") ?? "Europe/Paris");
    }

    /**
     * @throws \ReflectionException
     */
    public static function manageErrors(): void
    {
        $errorClass = new ReflectionClass(ErrorManager::class);

        $errorClass->newInstance();
    }

    public static function loadLang(string $package, ?string $lang): ?array
    {

        $package = ucfirst($package);

        $fileName = "App/Package/$package/Lang/$lang.php";

        if (!is_file($fileName)) {
            return null;
        }

        $fileContent = include $fileName;

        if (!is_array($fileContent)) {
            return null;
        }

        return $fileContent;
    }

    public static function listenRouter(): void
    {
        try {
            Router::getInstance()->listen();
        } catch (RouterException $e) {
            ErrorManager::showError($e->getCode());
            return;
        }
    }

    public static function loadRoutes($linkClass = Link::class): void
    {
        $attrList = self::getAttributeList()[$linkClass];

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
    public static function loadAttributes(): void
    {
        $files = Directory::getFilesRecursively("App/Package", "php");

        if (EnvManager::getInstance()->getValue('INSTALLSTEP') !== '-1') {
            $files = [...$files, ...Directory::getFilesRecursively("Installation", "php")];
        }

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
    public static function listAttributes(string $file): void
    {
        if (in_array($file, self::$fileLoadedAttr, true)) {
            return;
        }

        $className = PackageManager::getClassNamespaceFromPath($file);

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

                if (!isset(self::getAttributeListPointer()[$attribute->getName()])) {
                    self::getAttributeListPointer()[$attribute->getName()] = [];
                }

                self::getAttributeListPointer()[$attribute->getName()][] = [$attribute, $method];
            }
        }

        self::$fileLoadedAttr[] = $file;
    }

    public static function loadInstall(): void
    {
        if (is_dir("Installation")) {
            if (EnvManager::getInstance()->getValue("INSTALLSTEP") !== '-1') {
                ErrorManager::enableErrorDisplays(true);
                InstallerController::goToInstall();
            }
        } elseif (!EnvManager::getInstance()->getValue("DEVMODE")) {
            Directory::delete("Installation");
        }
    }

}
