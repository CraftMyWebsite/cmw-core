<?php

namespace CMW\Controller\Core;

use CMW\Entity\Core\PackageEntity;
use CMW\Entity\Core\PackageMenusEntity;
use CMW\Utils\Utils;
use JsonException;

class PackageController extends CoreController
{

    public static array $corePackages = ['Core', 'Pages', 'Users'];

    /**
     * @return PackageEntity[]
     * @desc Return packages they are not natives, like Core, Pages and Users
     */
    public static function getInstalledPackages(): array
    {
        $toReturn = array();
        $packagesFolder = 'App/Package/';
        $contentDirectory = array_diff(scandir("$packagesFolder/"), array('..', '.'));
        foreach ($contentDirectory as $package) {

            if (in_array($package, self::$corePackages, true)){
                continue;
            }

            if (file_exists("$packagesFolder/$package/infos.json") && !in_array($package, self::$corePackages, true)) {
                $toReturn[] = self::getPackage($package);
            }
        }

        return $toReturn;
    }

    /**
     * @return PackageEntity[]
     * @desc Return natives packages (core, users, pages) => self::$corePackages
     */
    public static function getCorePackages(): array
    {
        $toReturn = array();
        $packagesFolder = 'App/Package/';
        foreach (self::$corePackages as $package) {
            if (file_exists("$packagesFolder/$package/infos.json")) {
                $toReturn[] = self::getPackage($package);
            }
        }

        return $toReturn;
    }

    public static function getPackage(string $package): ?PackageEntity
    {
        
        if (!file_exists("App/Package/$package/infos.json")){
            return null;
        }

        try {
            $strJsonFileContents = file_get_contents("App/Package/$package/infos.json");
            $packageInfos = json_decode($strJsonFileContents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

        return new PackageEntity(
            $packageInfos['name'] ?? "",
            $packageInfos['description'] ?? "",
            $packageInfos['version'] ?? "",
            $packageInfos['author'] ?? "",
            self::getPackageMenus($package),
            $packageInfos['isGame'] ?? false,
            $packageInfos['isCore'] ?? false,
        );
    }

    /**
     * @param string $package
     * @return PackageMenusEntity[]
     */
    public static function getPackageMenus(string $package): array
    {
        try {
            $strJsonFileContents = file_get_contents("App/Package/$package/infos.json");
            $packageInfos = json_decode($strJsonFileContents, true, 512, JSON_THROW_ON_ERROR)['menus'];
        } catch (JsonException) {
            return [];
        }

        $toReturn = [];

        foreach ($packageInfos as $packageInfo):
            if (empty($packageInfo['url_menu'])) {
                $toReturn[] = new PackageMenusEntity(
                    $packageInfo['name_menu_' . Utils::getEnv()->getValue("LOCALE")],
                    $packageInfo['icon_menu'],
                    $packageInfo['url_menu'],
                    $packageInfo['urls_submenu_' . Utils::getEnv()->getValue("LOCALE")]
                );
            } else {
                $toReturn[] = new PackageMenusEntity(
                    $packageInfo['name_menu_' . Utils::getEnv()->getValue("LOCALE")],
                    $packageInfo['icon_menu'],
                    $packageInfo['url_menu'],
                    []
                );
            }
        endforeach;


        return $toReturn;
    }

    public static function isInstalled(string $package): bool
    {
        return self::getPackage($package) !== null;
    }

}
