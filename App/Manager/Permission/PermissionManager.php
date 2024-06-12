<?php

namespace CMW\Manager\Permission;

use RuntimeException;

class PermissionManager
{

    public static function canCreateFile(string $path): bool
    {
        self::createDirectory($path); //Create the log directory
        return is_writable($path); //todo test-it
    }

    /**
     * @param string $path
     * @return void
     * @desc Create the directory to store the Logs files
     */
    private static function createDirectory(string $path): void
    {
        if (!file_exists($path) && !mkdir($concurrentDirectory = $path) && !is_dir($concurrentDirectory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }

    /**
     * @return \CMW\Manager\Permission\IPermissionInit[]
     */
    public static function getPackagesPermissions(): array
    {
        $toReturn = [];
        $packagesFolder = 'App/Package/';
        $contentDirectory = array_diff(scandir("$packagesFolder/"), ['..', '.']);
        foreach ($contentDirectory as $package) {
            if (file_exists("$packagesFolder/$package/Init/Permissions.php")) {
                $permissions = self::getPackagePermissions($package);
                if (is_null($permissions)) {
                    continue;
                }
                $toReturn[] = $permissions;
            }
        }

        return $toReturn;
    }

    public static function getPackagePermissions(string $packageName): ?IPermissionInit
    {
        $namespace = 'CMW\\Permissions\\' . $packageName . '\\Permissions';

        if (!class_exists($namespace)) {
            return null;
        }

        $classInstance = new $namespace();

        if (!is_subclass_of($classInstance, IPermissionInit::class)) {
            return null;
        }

        return $classInstance;
    }

}