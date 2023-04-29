<?php

namespace CMW\Manager\Download;

use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Permission\PermissionManager;
use CMW\Model\Users\PermissionsModel;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\ExpectedValues;
use JsonException;
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

        $data = PublicAPI::getUrl() . $url;
        file_put_contents(Utils::getEnv()->getValue("DIR") . "Public/resource.zip",
            fopen($data, 'rb'));

        $archiveUpdate = new ZipArchive;
        if ($archiveUpdate->open(Utils::getEnv()->getValue("DIR") . 'Public/resource.zip') === TRUE) {

            if ($type === 'package') {
                $archiveUpdate->extractTo(Utils::getEnv()->getValue("DIR") . 'App/Package');
            } else {
                $archiveUpdate->extractTo(Utils::getEnv()->getValue("DIR") . 'Public/Themes');
            }

            $archiveUpdate->close();

            //Delete download archive
            unlink(Utils::getEnv()->getValue("DIR") . 'Public/resource.zip');


            //INSTALL INIT FOLDER
            if ($type === 'package'){
                self::initPackages($name);
            }

            return true;
        }

        return false;
    }

    public static function initPackages(string... $packages): void
    {
        foreach ($packages as $package):
            $initFolder = Utils::getEnv()->getValue("dir") . "App/Package/$package/Init";

            if (!is_dir($initFolder)) {
                continue;
            }

            $initFiles = array_diff(scandir($initFolder), array('..', '.'));

            if (empty($initFiles)) {
                continue;
            }

            // Load permissions files
            $permissionFile = "$initFolder/permissions.json";

            if (file_exists($permissionFile)){

                try {
                    $permissions = json_decode(file_get_contents($permissionFile), false, 512, JSON_THROW_ON_ERROR);

                    foreach ($permissions as $permission) {
                        (new PermissionsModel())->addFullCodePermission($permission);
                    }
                } catch (JsonException) {
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
                    $devMode = Utils::getEnv()->getValue("DEVMODE");

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

}