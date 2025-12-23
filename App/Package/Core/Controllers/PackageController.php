<?php

namespace CMW\Controller\Core;

use CMW\Controller\Core\Api\External\CheckerController;
use CMW\Controller\Users\UsersController;
use CMW\Exception\Core\Download\DownloadException;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Download\DownloadManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Notice\WarningManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Package\IPackageConfig;
use CMW\Manager\Package\IPackageConfigV2;
use CMW\Manager\Package\Adapter\LegacyPackageAdapter;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Manager\Views\View;
use CMW\Model\Core\ActivatedModel;
use CMW\Utils\Directory;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;
use function array_diff;
use function array_merge;
use function class_exists;
use function count;
use function file_exists;
use function file_get_contents;
use function in_array;
use function is_null;
use function scandir;

/**
 * Class: @PackageController
 * @package CORE
 * @author Teyir
 * @version 0.0.1
 */
class PackageController extends AbstractController
{
    public static array $corePackages = ['Core', 'Users', 'Pages'];
    private static ?array $ignoredEnvCache = null;

    /**
     * @return IPackageConfigV2[]
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
                $packageInstance = self::getPackage($package);
                if ($packageInstance !== null) {
                    $toReturn[] = $packageInstance;
                }
            }
        }

        return $toReturn;
    }

    /**
     * @return IPackageConfigV2[]
     * @desc Return natives packages (core, users) => self::$corePackages
     */
    public static function getCorePackages(): array
    {
        $toReturn = [];
        $packagesFolder = 'App/Package/';
        foreach (self::$corePackages as $package) {
            if (file_exists("$packagesFolder/$package/Package.php")) {
                $packageInstance = self::getPackage($package);
                if ($packageInstance !== null) {
                    $toReturn[] = $packageInstance;
                }
            }
        }

        return $toReturn;
    }

    /**
     * @return IPackageConfigV2[]
     * @desc Return getCorePackages() and getInstalledPackages()
     */
    public static function getAllPackages(): array
    {
        return array_merge(self::getCorePackages(), self::getInstalledPackages());
    }

    public static function getPackage(string $packageName): ?IPackageConfigV2
    {
        if (self::isDisabled($packageName)) {
            error_log("[CMW] Package '$packageName' USER RUN PACKAGE BUT API CMW NOT ALLOW THIS ON THIS DOMAIN : {$_SERVER['SERVER_NAME']}, SUPPORT D'ONT HELP THIS USER AND NOTIFY ADMIN QUICKLY !");
            WarningManager::addError("Le package <b>{$packageName}</b> a été désactivé. CraftMyWebsite a remarqué une tentative d'installation en contournant la vérification.<br>Votre domaine <b>{$_SERVER['SERVER_NAME']}</b> n'est pas autorisé pour <b>{$packageName}</b>.<br>Veuillez corriger cela rapidement sous peine de prendre des sanctions (blacklistage de votre site sur l'api de craftmywebsite.fr)");
            return null;
        }

        $namespace = 'CMW\\Package\\' . $packageName . '\Package';

        if (!class_exists($namespace)) {
            return null;
        }

        $classInstance = new $namespace();

        if ($classInstance instanceof IPackageConfigV2) {
            return $classInstance;
        }

        if ($classInstance instanceof IPackageConfig) {
            WarningManager::addError("Le package <b>{$packageName}</b> utilise l'ancienne interface <code>IPackageConfig</code>. Migre vers <code>IPackageConfigV2</code> ou met à jour le package pour rester compatible.<br><code>IPackageConfig</code> ne sera plus disponnible en beta-03");
            error_log("[CMW] Package '$packageName' Uses IPackageConfig (deprecated removed in beta-03). Consider migrating to IPackageConfigV2. or update the package if you haven't already!");
            return new LegacyPackageAdapter($classInstance);
        }

        return null;
    }

    public static function isInstalled(string $package): bool
    {
        return self::getPackage($package) !== null;
    }

    public static function getIgnoredPackages(): array
    {
        if (self::$ignoredEnvCache !== null) {
            return self::$ignoredEnvCache;
        }

        $env = EnvManager::getInstance()->getValue('DISABLED_PACKAGE') ?: '';
        $list = array_filter(array_map('trim', explode(',', $env)));
        self::$ignoredEnvCache = array_values(array_unique($list));

        return self::$ignoredEnvCache;
    }

    public static function isDisabled(string $package): bool
    {
        foreach (self::getIgnoredPackages() as $p) {
            if (strcasecmp($p, $package) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     * @desc Return the list of public packages from our market
     */
    public static function getMarketPackages(): array
    {
        if (UpdatesManager::isTestAPI()) {
            return PublicAPI::getData('market/resources/all/states/1');
        }
        return PublicAPI::getData('market/resources/filtered/1');
    }

    /**
     * @return IPackageConfigV2[]
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

    #[Link('/market', Link::GET, [], '/cmw-admin/packages')]
    private function adminPackageManage(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.packages.market');
        CheckerController::getInstance()->checkActivationAPI();

        $installedPackages = self::getInstalledPackages();
        $packagesList = array_filter(self::getMarketPackages(), static function ($pkg) {
            return !self::isInstalled($pkg['name']) && !self::isDisabled($pkg['name']);
        });

        View::createAdminView('Core', 'Package/market')
            ->addVariableList(['installedPackages' => $installedPackages, 'packagesList' => $packagesList])
            ->view();
    }

    #[Link('/package', Link::GET, [], '/cmw-admin/packages')]
    private function adminMyPackage(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.packages.manage');
        CheckerController::getInstance()->checkActivationAPI();

        $installedPackages = self::getInstalledPackages();
        $packagesList = self::getMarketPackages();

        View::createAdminView('Core', 'Package/package')
            ->addVariableList(['installedPackages' => $installedPackages, 'packagesList' => $packagesList])
            ->view();
    }

    #[NoReturn]
    #[Link('/install', Link::POST, [], '/cmw-admin/packages')]
    private function adminPackageInstallation(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.packages.market');

        if (!EnvManager::getInstance()->getValue('DEVMODE')) {
            $CoreNeedUpdate = UpdatesManager::checkNewUpdateAvailable();
            if ($CoreNeedUpdate) {
                Flash::send(Alert::ERROR, 'CORE', LangManager::translate('core.toaster.package.updateBeforeUpdate'));
                Redirect::redirect('cmw-admin/updates/cms');
            }
        }

        $id = FilterManager::filterInputIntPost('resId');
        $status = FilterManager::filterInputStringPost('status');
        $activationKey = FilterManager::filterInputStringPost('activationKey');

        if ($status === 'online') {
            $status = 0;
        } else {
            $status = 1;
        }

        // Check market dependencies
        $thisPackage = PublicAPI::getData("market/resources/$id");

        $missing = [];
        if (!empty($thisPackage['dependencies'])) {
            foreach ($thisPackage['dependencies'] as $dep) {
                if (is_null(PackageController::getPackage($dep['market_name'] ?? $dep['name'] ?? null))) {
                    $missing[] = '<b>'.($dep['market_name'] ?? $dep['name']).'</b>';
                }
            }

            if (!empty($missing)) {
                $count = count($missing);
                $list  = $count > 1
                    ? implode(', ', array_slice($missing, 0, -1)).' et '.end($missing)
                    : $missing[0];

                $label = $count > 1 ? 'les packages ' : 'le package ';

                Flash::send(
                    Alert::WARNING,
                    'Packages',
                    'Veuillez installer '.$label.$list.' avant d\'installer <b>'.$thisPackage['market_name'].
                    '</b> car il en a besoin pour fonctionner.'
                );
                Redirect::redirectPreviousRoute();
            }

            // Check versions of installed dependencies update before install
            $blocking = [];
            foreach ($thisPackage['dependencies'] as $dep) {
                // Récup local
                $local = self::getPackage($dep['market_name'] ?? $dep['name'] ?? null);
                if ($local === null) {
                    continue;
                }

                $depIdOrSlug = $dep['id'] ?? ($dep['name'] ?? null);
                $depApi = $depIdOrSlug ? PublicAPI::getData("market/resources/{$depIdOrSlug}") : null;
                if (!is_array($depApi) || empty($depApi['version_name'])) {
                    $blocking[] = "<b>".($dep['market_name'] ?? $dep['name'])."</b> (version distante inconnue)";
                    continue;
                }

                $remote = ltrim((string)$depApi['version_name'], "vV");
                $localV = ltrim((string)$local->version(), "vV");

                if (version_compare($localV, $remote, '<')) {
                    $blocking[] = "<b>".($dep['market_name'] ?? $dep['name'])."</b> {$localV} ➜ {$remote}";
                }
            }

            if (!empty($blocking)) {
                $count = count($blocking);
                $list  = $count > 1
                    ? implode(', ', array_slice($blocking, 0, -1)).' et '.end($blocking)
                    : $blocking[0];

                $label = $count > 1 ? 'les packages ' : 'le package ';

                Flash::send(
                    Alert::WARNING,
                    'Packages',
                    "Veuillez d'abord mettre à jour {$label}{$list} avant d'installer <b>{$thisPackage['market_name']}</b>."
                );
                Redirect::redirectPreviousRoute();
            }
        }

        $data = [
            'resId' => $id,
            'status' => $status,
            'activationKey' => $activationKey,
        ];

        $package = PublicAPI::postData("market/resources/install" , $data);

        if (isset($package['error'])) {
            $code = $package['error']['code'] ?? 'UNKNOWN';
            $desc = $package['error']['description']['Description']
                ?? $package['error']['description']['description']
                ?? ($package['error']['info'] ?? 'Erreur inconnue');

            Flash::send(Alert::ERROR, "Erreur ".$code, $desc);
            Redirect::redirectPreviousRoute();
        } elseif (!empty($package['file'])) {
            try {
                DownloadManager::installPackageWithLink($package['file'], 'package', $package['name']);
            } catch (DownloadException $e) {
                Flash::send(
                    Alert::ERROR,
                    LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.toaster.theme.unableUpdate') . $e->getMessage(),
                );
                Redirect::redirectPreviousRoute();
            }
            if (!empty($activationKey)) {
                ActivatedModel::getInstance()->addActivation(EncryptManager::encrypt($activationKey), $thisPackage['id'], $thisPackage['name']);
            }
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate('core.Package.toasters.install.success', ['package' => $package['name']]));
        } else {
            Flash::send(Alert::ERROR, "Erreur", "Une erreur est survenue sur l'API, contacte le support de CraftMyWebsite.");
        }

        sleep(2);

        Redirect::redirectPreviousRoute();
    }

    #[Link('/delete/:package', Link::GET, ['package' => '.*?'], '/cmw-admin/packages')]
    #[NoReturn]
    private function adminPackageDelete(string $package): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.packages.manage');

        if (!$this->uninstallPackage($package)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.Package.toasters.delete.error',
                    ['package' => $package]));
            Redirect::redirectPreviousRoute();
        }

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.Package.toasters.delete.success',
                ['package' => $package]));
        ActivatedModel::getInstance()->removeActivationByResName($package);
        Redirect::redirectPreviousRoute();
    }

    #[Link('/update', Link::POST, [], '/cmw-admin/packages')]
    #[NoReturn]
    private function adminPackageUpdate(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.packages.manage');

        if (!EnvManager::getInstance()->getValue('DEVMODE')) {
            $CoreNeedUpdate = UpdatesManager::checkNewUpdateAvailable();
            if ($CoreNeedUpdate) {
                Flash::send(Alert::ERROR, 'CORE', LangManager::translate('core.toaster.package.updateBeforeUpdate'));
                Redirect::redirect('cmw-admin/updates/cms');
            }
        }

        $id = FilterManager::filterInputIntPost('resId');
        $actualVersion = FilterManager::filterInputStringPost('localVersion');
        $packageName = FilterManager::filterInputStringPost('packageName');
        $status = FilterManager::filterInputStringPost('status');

        $statusInt = ($status === 'test') ? 1 : 0;

        //Check if dependencies have update before update this
        $current = PublicAPI::getData("market/resources/$id");
        $blocking = [];

        if (!empty($current['dependencies'])) {
            foreach ($current['dependencies'] as $dep) {
                $local = self::getPackage($dep['market_name']);
                if ($local === null) {
                    $blocking[] = "<b>{$dep['market_name']}</b> (non installé)";
                    continue;
                }

                $depApi = PublicAPI::getData("market/resources/{$dep['id']}");
                if (!is_array($depApi) || empty($depApi['version_name'])) {
                    $blocking[] = "<b>{$dep['market_name']}</b> (version distante inconnue)";
                    continue;
                }

                $remote = ltrim((string)$depApi['version_name'], "vV");
                $localV = ltrim((string)$local->version(), "vV");

                if (version_compare($localV, $remote, '<')) {
                    $blocking[] = "<b>{$dep['market_name']}</b> {$localV} ➜ {$remote}";
                }
            }

            if (!empty($blocking)) {
                $count = count($blocking);
                $list  = $count > 1
                    ? implode(', ', array_slice($blocking, 0, -1)).' et '.end($blocking)
                    : $blocking[0];

                $label = $count > 1 ? 'les packages ' : 'le package ';

                Flash::send(
                    Alert::WARNING,
                    'Packages',
                    "Veuillez d'abord mettre à jour {$label}{$list} avant d'actualiser <b>{$packageName}</b>."
                );
                Redirect::redirectPreviousRoute();
            }
        }

        $activation = ActivatedModel::getInstance()->getActivationByResId($id);
        $activationKey = $activation['resource_key'] ?? null;
        if ($activationKey) {
            $decryptedActivationKey = EncryptManager::decrypt($activationKey);
        } else {
            $decryptedActivationKey = null;
        }

        $data = [
            'resId'         => $id,
            'version'       => $actualVersion,
            'status'        => $statusInt,
            'activationKey' => $decryptedActivationKey,
        ];

        $updates = PublicAPI::postData("market/resources/updates", $data);

        if (isset($updates['error'])) {
            $code = $updates['error']['code'] ?? 'UNKNOWN';
            $desc = $updates['error']['description']['Description']
                ?? $updates['error']['description']['description']
                ?? ($updates['error']['info'] ?? 'Erreur inconnue');

            Flash::send(Alert::ERROR, "Erreur ".$code, $desc);
            Redirect::redirectPreviousRoute();
        }

        if (empty($updates)) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                "No updates available for this package",
            );
            Redirect::redirectPreviousRoute();
        }

        // Update package
        if (!Directory::delete(EnvManager::getInstance()->getValue('DIR') . "App/Package/$packageName")) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.theme.unableDeleteFolder') . EnvManager::getInstance()->getValue('DIR') . "App/Package/$packageName",
            );
            Redirect::redirectPreviousRoute();
        }

        $lastUpdateIndex = count($updates) - 1;
        foreach ($updates as $i => $update) {
            if (!empty($update['sql_updater'])) {
                $file = file_get_contents($update['sql_updater']);

                if (!$file) {
                    Flash::send(
                        Alert::ERROR,
                        LangManager::translate('core.toaster.error'),
                        $update['sql_updater'],
                    );
                    Redirect::redirectPreviousRoute();
                }

                DatabaseManager::getLiteInstance()->query($file);
            }

            if ($i === $lastUpdateIndex) {
                try {
                    DownloadManager::installPackageWithLink($update['file'], 'package', $packageName, false);
                } catch (DownloadException $e) {
                    Flash::send(
                        Alert::ERROR,
                        LangManager::translate('core.toaster.error'),
                        LangManager::translate('core.toaster.theme.unableUpdate') . $e->getMessage(),
                    );
                    Redirect::redirectPreviousRoute();
                }
            }
        }

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.Package.toasters.update.success', ['package' => $packageName]));

        //Reload too fast redirect not refresh correctly
        sleep(5);

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

        // We can't delete core packages
        if (in_array($package, self::$corePackages, true)) {
            return false;
        }

        // First we uninstall DB
        $uninstallSqlFile = EnvManager::getInstance()->getValue('DIR') . "App/Package/$packageName/Init/uninstall.sql";

        if (file_exists($uninstallSqlFile)) {
            $db = DatabaseManager::getLiteInstance();

            $querySqlFile = file_get_contents($uninstallSqlFile);
            $req = $db->query($querySqlFile);

            if (!$req) {
                return false;
            }

            $req->closeCursor();
        }

        // Check Package uninstall override
        if (!$package->uninstall()) {
            return false;
        }

        // Uninstall package:
        return Directory::delete(EnvManager::getInstance()->getValue('DIR') . "App/Package/$packageName");
    }
}
