<?php

namespace CMW\Manager\Download;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Permission\PermissionManager;
use CMW\Model\Users\PermissionsModel;
use CMW\Utils\Directory;
use CMW\Utils\Log;
use JetBrains\PhpStorm\ExpectedValues;
use ZipArchive;

class DownloadManager
{

    /**
     * @param string $url
     * @param string $type
     * @param string $name
     * @return bool
     * @desc Download and install package with api return link, ex: "/Public/market/Resources/forum.zip"
     */
    public static function installPackageWithLink(string $url, #[ExpectedValues(['package', 'Theme'])] string $type, string $name): bool
    {

        if (!in_array($type, ['package', 'Theme'])) {
            return false;
        }

        file_put_contents(EnvManager::getInstance()->getValue("DIR") . "Public/resource.zip",
            fopen($url, 'rb'));

        $archiveUpdate = new ZipArchive;
        if ($archiveUpdate->open(EnvManager::getInstance()->getValue("DIR") . 'Public/resource.zip') === TRUE) {

            if ($type === 'package') {
                $archiveUpdate->extractTo(EnvManager::getInstance()->getValue("DIR") . 'App/Package');
            } else {
                $archiveUpdate->extractTo(EnvManager::getInstance()->getValue("DIR") . 'Public/Themes');
            }

            $archiveUpdate->close();

            //Delete download archive
            unlink(EnvManager::getInstance()->getValue("DIR") . 'Public/resource.zip');


            //INSTALL INIT FOLDER
            if ($type === 'package') {
                self::initPackages($name);
            }

            return true;
        }

        return false;
    }

    public static function initPackages(string...$packages): void
    {
        foreach ($packages as $package):
            $initFolder = EnvManager::getInstance()->getValue("dir") . "App/Package/$package/Init";

            if (!is_dir($initFolder)) {
                continue;
            }

            $initFiles = array_diff(scandir($initFolder), ['..', '.']);

            if (empty($initFiles)) {
                continue;
            }

            // Load permissions files
            $packagePermissions = PermissionManager::getPackagePermissions($package);

            if (!is_null($packagePermissions)) {
                foreach ($packagePermissions->permissions() as $permission) {
                    PermissionsModel::getInstance()->addFullCodePermission($permission);
                }
            }

            // Load sql files
            foreach ($initFiles as $sqlFile) {

                $packageSqlFile = "$initFolder/$sqlFile";

                if (!is_file($packageSqlFile) || pathinfo($packageSqlFile, PATHINFO_EXTENSION) !== "sql") {
                    continue;
                }

                if (file_exists($packageSqlFile)) {
                    $db = DatabaseManager::getLiteInstance();
                    $devMode = EnvManager::getInstance()->getValue("DEVMODE");

                    $querySqlFile = file_get_contents($packageSqlFile);
                    $req = $db->query($querySqlFile);
                    $req->closeCursor();

                    if ($devMode === '0') {
                        unlink($packageSqlFile);
                    }
                }

            }
        endforeach;
    }

    /**
     * @param string $packageName
     * @return bool
     */
    public static function deletePackage(string $packageName): bool
    {
        $path = EnvManager::getInstance()->getValue('DIR') . "App/Package/$packageName";

        //TODO Delete database with a new file 'uninstall.sql' ??

        return Directory::delete($path);
    }

}