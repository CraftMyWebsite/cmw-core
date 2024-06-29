<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Download\DownloadManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Requests\Request;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Utils\Directory;
use CMW\Utils\Log;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

class PackageController extends AbstractController
{

    public static array $corePackages = ['Core', 'Users'];

    /**
     * @return IPackageConfig[]
     * @desc Return packages they are not natives, like Core and Users
     */
    public static function getInstalledPackages(): array
    {
        $toReturn = [];
        $packagesFolder = 'App/Package/';
        $contentDirectory = array_diff(scandir("$packagesFolder/"), ['..', '.']);
        foreach ($contentDirectory as $package) {

            if (in_array($package, self::$corePackages, true)) {
                continue;
            }

            if (file_exists("$packagesFolder/$package/Package.php") && !in_array($package, self::$corePackages, true)) {
                $toReturn[] = self::getPackage($package);
            }
        }

        return $toReturn;
    }

    /**
     * @return IPackageConfig[]
     * @desc Return natives packages (core, users) => self::$corePackages
     */
    public static function getCorePackages(): array
    {
        $toReturn = [];
        $packagesFolder = 'App/Package/';
        foreach (self::$corePackages as $package) {
            if (file_exists("$packagesFolder/$package/Package.php")) {
                $toReturn[] = self::getPackage($package);
            }
        }

        return $toReturn;
    }

    /**
     * @return IPackageConfig[]
     * @desc Return getCorePackages() and getInstalledPackages()
     */
    public static function getAllPackages(): array
    {
        return array_merge(self::getCorePackages(), self::getInstalledPackages());
    }

    public static function getPackage(string $packageName): ?IPackageConfig
    {
        $namespace = 'CMW\\Package\\' . $packageName . "\\Package";

        if (!class_exists($namespace)) {
            return null;
        }

        $classInstance = new $namespace();

        if (!is_subclass_of($classInstance, IPackageConfig::class)) {
            return null;
        }

        return $classInstance;
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
        return PublicAPI::getData("market/resources/filtered/1");
    }

    /**
     * @return IPackageConfig[]
     * @desc Return all packages local (remove packages get from the public market)
     */
    public static function getLocalPackages(): array
    {
        $toReturn = [];
        $installedPackages = self::getInstalledPackages();

        $marketPackagesName = [];

        foreach (self::getMarketPackages() as $marketTheme):
            $marketPackagesName[] = $marketTheme['name'];
        endforeach;

        foreach ($installedPackages as $installedPackage):
            if (!in_array($installedPackage->name(), $marketPackagesName, true)):
                $toReturn[] = $installedPackage;
            endif;
        endforeach;

        return $toReturn;
    }

    /* ADMINISTRATION */

    #[Link("/market", Link::GET, [], "/cmw-admin/packages")]
    private function adminPackageManage(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.packages.market");

        $installedPackages = self::getInstalledPackages();
        $packagesList = self::getMarketPackages();

        View::createAdminView("Core", "Package/market")
            ->addVariableList(["installedPackages" => $installedPackages, "packagesList" => $packagesList])
            ->view();
    }

    #[Link("/package", Link::GET, [], "/cmw-admin/packages")]
    private function adminMyPackage(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.packages.manage");

        $installedPackages = self::getInstalledPackages();
        $packagesList = self::getMarketPackages();

        View::createAdminView("Core", "Package/package")
            ->addVariableList(["installedPackages" => $installedPackages, "packagesList" => $packagesList])
            ->view();
    }

    #[Link("/install/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin/packages")]
    #[NoReturn] private function adminPackageInstallation(Request $request, int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.packages.market");

        $package = PublicAPI::putData("market/resources/install/$id");

        if (empty($package)) {
            Flash::send(Alert::ERROR, LangManager::translate("core.toaster.error"),
                LangManager::translate("core.toaster.internalError") . ' (API)');
            Redirect::redirectPreviousRoute();
        }

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

    #[Link("/delete/:package", Link::GET, ["package" => ".*?"], "/cmw-admin/packages")]
    #[NoReturn] private function adminPackageDelete(Request $request, string $package): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.packages.manage");

        if (!$this->uninstallPackage($package)) {
            Flash::send(Alert::ERROR, LangManager::translate("core.package.error"),
                LangManager::translate("core.Package.toasters.delete.error",
                    ['package' => $package]));
            Redirect::redirectPreviousRoute();
        }

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("core.Package.toasters.delete.success",
                ['package' => $package]));

        Redirect::redirectPreviousRoute();
    }

    #[Link("/update/:id/:actualVersion/:packageName", Link::GET, ["id" => "[0-9]+", "actualVersion" => ".*?", "packageName" => ".*?"], "/cmw-admin/packages")]
    #[NoReturn] private function adminPackageUpdate(Request $request, int $id, string $actualVersion, string $packageName): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.packages.manage");

        $updates = PublicAPI::getData("market/resources/updates/$id/$actualVersion");

        if (isset($updates['error'])) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'), $updates['error']['code']);
            Redirect::redirectPreviousRoute();
        }

        //Update package

        Directory::delete(EnvManager::getInstance()->getValue('DIR') . "App/Package/$packageName");

        $lastUpdateIndex = count($updates) - 1;
        foreach ($updates as $i => $update) {
            if (!empty($update['sql_updater'])) {
                DatabaseManager::getLiteInstance()->query($update['sql_updater']);
            }

            if ($i === $lastUpdateIndex) {
                DownloadManager::installPackageWithLink($update['file'], 'package', $packageName);
            }
        }

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate('core.Package.toasters.update.success', ['package' => $packageName]));

        Redirect::redirectPreviousRoute();
    }

    /**
     * @param string $packageName
     * @return bool
     * @desc
     * <p>Uninstall package (sql and override methods)</p>
     */
    private function uninstallPackage(string $packageName): bool
    {
        $package = self::getPackage($packageName);

        if (is_null($package)) {
            return false;
        }

        //We can't delete core packages
        if (in_array($package, self::$corePackages, true)) {
            return false;
        }

        //First we uninstall DB
        $uninstallSqlFile = EnvManager::getInstance()->getValue('DIR') . "App/Package/$packageName/Init/uninstall.sql";

        if (file_exists($uninstallSqlFile)) {
            $sql = file_get_contents($uninstallSqlFile);
            $db = DatabaseManager::getInstance();

            if (!$db->query($sql)) {
                return false;
            }
        }

        //Check Package uninstall override
        if (!$package->uninstall()) {
            return false;
        }

        //Uninstall package:
        return Directory::delete(EnvManager::getInstance()->getValue('DIR') . "App/Package/$packageName");
    }

}
