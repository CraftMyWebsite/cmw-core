<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Entity\Core\PackageEntity;
use CMW\Entity\Core\PackageMenusEntity;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Download\DownloadManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Utils\Directory;
use CMW\Utils\Log;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

class PackageController extends AbstractController
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

            if (in_array($package, self::$corePackages, true)) {
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

    /**
     * @return array
     * @desc Return getCorePackages() and getInstalledPackages()
     */
    public static function getAllPackages(): array
    {
        return array_merge(self::getCorePackages(), self::getInstalledPackages());
    }

    public static function getPackage(string $package): ?PackageEntity
    {

        if (!file_exists("App/Package/$package/infos.json")) {
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
                    $packageInfo['name_menu_' . EnvManager::getInstance()->getValue("LOCALE")],
                    $packageInfo['icon_menu'],
                    $packageInfo['url_menu'],
                    $packageInfo['urls_submenu_' . EnvManager::getInstance()->getValue("LOCALE")]
                );
            } else {
                $toReturn[] = new PackageMenusEntity(
                    $packageInfo['name_menu_' . EnvManager::getInstance()->getValue("LOCALE")],
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

    /**
     * @return array
     * @desc Return the list of public packages from our market
     */
    public static function getMarketPackages(): array
    {
        return PublicAPI::getData("resources/getResources&resource_type=1");
    }

    /**
     * @return PackageEntity[]
     * @desc Return all packages local (remove packages get from the public market)
     */
    public static function getLocalPackages(): array
    {
        $toReturn = array();
        $installedPackages = self::getInstalledPackages();

        $marketPackagesName = array();

        foreach (self::getMarketPackages() as $marketTheme):
            $marketPackagesName[] = $marketTheme['name'];
        endforeach;

        foreach ($installedPackages as $installedPackage):
            if (!in_array($installedPackage->getName(), $marketPackagesName, true)):
                $toReturn[] = $installedPackage;
            endif;
        endforeach;

        return $toReturn;
    }

    /* ADMINISTRATION */

    #[Link("/", Link::GET, [], "/cmw-admin/packages")]
    private function adminPackageManage(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.Theme.configuration");

        $installedPackages = self::getInstalledPackages();
        $packagesList = self::getMarketPackages();

        View::createAdminView("Core", "package")
            ->addVariableList(["installedPackages" => $installedPackages, "packagesList" => $packagesList])
            ->view();
    }

    #[Link("/install/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/packages")]
    #[NoReturn] private function adminPackageInstallation(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.packages.configuration");

        $package = PublicAPI::getData("resources/installResource&id=$id");



        if (!DownloadManager::installPackageWithLink($package['file'], "package", $package['name'])) {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate("core.downloads.errors.internalError",
                    ['name' => $package['name'], 'version' => $package['version_name']]));
            Redirect::redirectPreviousRoute();
        }


        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate('core.Package.toasters.install.success', ['package' => $package['name']]));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/update/:id/:actualVersion/:packageName", Link::GET, ["id" => "[0-9]+", "actualVersion" => ".*?", "packageName" => ".*?"], "/cmw-admin/packages")]
    #[NoReturn] private function adminPackageUpdate(Request $request, int $id, string $actualVersion, string $packageName): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.packages.configuration");

        $updates = PublicAPI::getData("resources/getResourceUpdates&id=$id&actualVersion=$actualVersion");

        Log::debug($updates);

        //Update package

        Directory::delete(EnvManager::getInstance()->getValue('DIR') . "App/Package/$packageName");

        $lastUpdateIndex = count($updates) - 1;
        foreach ($updates as $i => $update) {
            if ($update['sql_updater'] !== null){
                DatabaseManager::getLiteInstance()->query($update['sql_updater']);
            }

            if ($i === $lastUpdateIndex){
                DownloadManager::installPackageWithLink($update['file'], 'package', $packageName);
            }
        }

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate('core.Package.toasters.update.success', ['package' => $packageName]));

        Redirect::redirectPreviousRoute();
    }

}
