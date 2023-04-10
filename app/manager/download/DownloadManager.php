<?php

namespace CMW\Manager\Download;

use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Database\DatabaseManager;
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
     * @desc Download and install package with api return link, ex: "/public/market/resources/forum.zip"
     */
    public static function installPackageWithLink(string $url, #[ExpectedValues(['package', 'theme'])] string $type, string $name): bool
    {

        if (!in_array($type, ['package', 'theme'])) {
            return false;
        }

        $data = PublicAPI::getUrl() . $url;
        file_put_contents(Utils::getEnv()->getValue("DIR") . "public/resource.zip",
            fopen($data, 'rb'));

        $archiveUpdate = new ZipArchive;
        if ($archiveUpdate->open(Utils::getEnv()->getValue("DIR") . 'public/resource.zip') === TRUE) {

            if ($type === 'package') {
                $archiveUpdate->extractTo(Utils::getEnv()->getValue("DIR") . 'app/package');
            } else {
                $archiveUpdate->extractTo(Utils::getEnv()->getValue("DIR") . 'public/themes');
            }

            $archiveUpdate->close();

            //Delete download archive
            unlink(Utils::getEnv()->getValue("DIR") . 'public/resource.zip');


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
            $initFolder = Utils::getEnv()->getValue("dir") . "app/package/$package/init";

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
                //TODO install perm with new system
            }


            // Load sql files
            foreach ($initFiles as $sqlFile) {

                $packageSqlFile = "$initFolder/$sqlFile";

                if (!is_file($packageSqlFile) || pathinfo($packageSqlFile, PATHINFO_EXTENSION) !== "sql") {
                    continue;
                }

                if (file_exists($packageSqlFile)) {
                    $db = DatabaseManager::getInstance();
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