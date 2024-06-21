<?php

namespace CMW\Manager\Download;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Permission\PermissionManager;
use CMW\Model\Users\PermissionsModel;
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

            // Load sql file
            $sqlFile = "$initFolder/init.sql";
            if (file_exists($sqlFile)) {
                $db = DatabaseManager::getLiteInstance();
                $devMode = EnvManager::getInstance()->getValue("DEVMODE");

                $querySqlFile = file_get_contents($sqlFile);
                $req = $db->query($querySqlFile);
                $req->closeCursor();

                if ($devMode === '0') {
                    unlink($sqlFile);
                }
            }

        endforeach;
    }

}