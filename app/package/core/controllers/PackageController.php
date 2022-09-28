<?php

namespace CMW\Controller\Core;

use CMW\Entity\Core\PackageEntity;
use JsonException;

class PackageController extends CoreController
{

    public static function getInstalledPackages(): array
    {
        $toReturn = array();
        $packagesFolder = 'app/package/';
        $contentDirectory = array_diff(scandir("$packagesFolder/"), array('..', '.'));
        foreach ($contentDirectory as $package) {
            if(file_exists("$packagesFolder/$package/infos.json")) {
                $toReturn[] = self::getPackage($package);
            }
        }

        return $toReturn;
    }

    public static function getPackage(string $package): ?PackageEntity
    {

        try {
            $strJsonFileContents = file_get_contents("app/package/$package/infos.json");
            $packageInfos = json_decode($strJsonFileContents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return null;
        }

        return new PackageEntity(
                $packageInfos['name'] ?? "",
                $packageInfos['description'] ?? "",
                $packageInfos['version'] ?? "",
                $packageInfos['author'] ?? "",
        );
    }

    public static function isInstalled(string $package): bool
    {
        return (file_exists("app/package/$package/infos.json"));
    }

}
