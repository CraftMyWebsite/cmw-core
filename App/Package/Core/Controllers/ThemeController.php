<?php

namespace CMW\Controller\Core;

use CMW\Controller\Core\Api\External\CheckerController;
use CMW\Controller\Users\UsersController;
use CMW\Exception\Core\Download\DownloadException;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Download\DownloadManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Theme\Config\ThemeMapper;
use CMW\Manager\Theme\Config\ThemeSettingsMapper;
use CMW\Manager\Theme\Editor\ThemeEditorProcessor;
use CMW\Manager\Theme\File\ThemeFileManager;
use CMW\Manager\Theme\Loader\ThemeLoader;
use CMW\Manager\Theme\Market\ThemeMarketManager;
use CMW\Manager\Theme\ThemeManager;
use CMW\Manager\Theme\UninstallThemeType;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Core\ActivatedModel;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Directory;
use CMW\Utils\Redirect;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use function base64_decode;

/**
 * Class: @ThemeController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 0.0.1
 */
class ThemeController extends AbstractController
{
    /* ADMINISTRATION */
    #[Link('/market', Link::GET, [], '/cmw-admin/theme')]
    private function adminThemeMarket(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.market');
        CheckerController::getInstance()->checkActivationAPI();

        $currentTheme = ThemeLoader::getInstance()->getCurrentTheme();
        $installedThemes = ThemeLoader::getInstance()->getInstalledThemes();
        $themesList = ThemeMarketManager::getInstance()->getMarketThemes();

        $themeConfigs = ThemeModel::getInstance()->getInstance()->fetchThemeConfigs($currentTheme->name());
        SimpleCacheManager::storeCache($themeConfigs, 'config', 'Themes/' . $currentTheme->name());

        View::createAdminView('Core', 'Theme/market')
            ->addVariableList(['currentTheme' => $currentTheme, 'installedThemes' => $installedThemes, 'themesList' => $themesList])
            ->view();
    }

    #[Link(path: '/', method: Link::GET, scope: '/cmw-admin/theme')]
    #[Link('/theme', Link::GET, [], '/cmw-admin/theme')]
    private function adminThemeConfiguration(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.manage');
        CheckerController::getInstance()->checkActivationAPI();

        $currentTheme = ThemeLoader::getInstance()->getCurrentTheme();
        $installedThemes = ThemeLoader::getInstance()->getInstalledThemes();
        $themesList = ThemeMarketManager::getInstance()->getMarketThemes();

        $themeConfigs = ThemeModel::getInstance()->getInstance()->fetchThemeConfigs($currentTheme->name());
        SimpleCacheManager::storeCache($themeConfigs, 'config', 'Themes/' . $currentTheme->name());

        View::createAdminView('Core', 'Theme/themes')
            ->addVariableList(['currentTheme' => $currentTheme, 'installedThemes' => $installedThemes, 'themesList' => $themesList])
            ->view();
    }

    #[NoReturn]
    #[Link('/theme', Link::POST, [], '/cmw-admin/theme')]
    private function adminThemeConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.edit');

        $theme = FilterManager::filterInputStringPost('theme', 50);

        CoreModel::getInstance()->updateOption('theme', $theme);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.config.success'));

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/market/regenerate', Link::GET, [], '/cmw-admin/theme')]
    private function adminThemeConfigurationRegenerate(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.edit');

        $themeName = ThemeLoader::getInstance()->getCurrentTheme()->name();
        ThemeModel::getInstance()->getInstance()->deleteThemeConfig($themeName);
        ThemeFileManager::getInstance()->updateThemeSettings($themeName);

        $themeConfigs = ThemeModel::getInstance()->getInstance()->fetchThemeConfigs($themeName);
        SimpleCacheManager::storeCache($themeConfigs, 'config', 'Themes/' . $themeName);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.theme.regenerate'));

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/install', Link::POST, [], '/cmw-admin/theme')]
    private function adminThemeInstallation(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.manage');

        if (!EnvManager::getInstance()->getValue('DEVMODE')) {
            $CoreNeedUpdate = UpdatesManager::checkNewUpdateAvailable();
            if ($CoreNeedUpdate) {
                Flash::send(Alert::ERROR, 'CORE', LangManager::translate('core.toaster.theme.updateBeforeInstall'));
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
        $thisTheme = PublicAPI::getData("market/resources/$id");

        $missing = [];
        if (!empty($thisTheme['dependencies'])) {
            foreach ($thisTheme['dependencies'] as $dep) {
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
                    'Veuillez installer '.$label.$list.' avant d\'installer <b>'.$thisTheme['market_name'].
                    '</b> car il en a besoin pour fonctionner.'
                );
                Redirect::redirectPreviousRoute();
            }

            // Check versions of installed dependencies update before install
            $blocking = [];
            foreach ($thisTheme['dependencies'] as $dep) {
                // Récup local
                $local = ThemeLoader::getInstance()->getTheme($dep['name'] ?? null);
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
                    "Veuillez d'abord mettre à jour {$label}{$list} avant d'installer <b>{$thisTheme['market_name']}</b>."
                );
                Redirect::redirectPreviousRoute();
            }
        }

        $data = [
            'resId' => $id,
            'status' => $status,
            'activationKey' => $activationKey,
        ];

        $theme = PublicAPI::postData("market/resources/install" , $data);

        if (isset($theme['error'])) {
            $code = $theme['error']['code'] ?? 'UNKNOWN';
            $desc = $theme['error']['description']['Description']
                ?? $theme['error']['description']['description']
                ?? ($theme['error']['info'] ?? 'Erreur inconnue');

            Flash::send(Alert::ERROR, "Erreur ".$code, $desc);
            Redirect::redirectPreviousRoute();
        } elseif (!empty($theme['file'])) {
            try {
                DownloadManager::installPackageWithLink($theme['file'], 'Theme', $theme['name']);
            } catch (DownloadException $e) {
                Flash::send(
                    Alert::ERROR,
                    LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.toaster.theme.unableUpdate') . $e->getMessage(),
                );
                Redirect::redirectPreviousRoute();
            }
        } else {
            Flash::send(Alert::ERROR, "Erreur", "Une erreur est survenue sur l'API, contacte le support de CraftMyWebsite.");
        }

        if (!empty($activationKey)) {
            ActivatedModel::getInstance()->addActivation(EncryptManager::encrypt($activationKey), $thisTheme['id'], $thisTheme['name']);
        }
        // Install Theme settings
        ThemeFileManager::getInstance()->installThemeSettings($theme['name']);
        CoreModel::getInstance()->updateOption('theme', $theme['name']);

        $themeConfigs = ThemeModel::getInstance()->getInstance()->fetchThemeConfigs($theme['name']);
        SimpleCacheManager::storeCache($themeConfigs, 'config', 'Themes/' . $theme['name']);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.theme.installed', ['theme' => $theme['name']]));

        sleep(2);

        Redirect::redirect('cmw-admin/theme/manage');
    }

    #[Link('/manage', Link::GET, [], '/cmw-admin/theme')]
    private function adminThemeManage(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.edit');
        CheckerController::getInstance()->checkActivationAPI();

        //Vérifie si la valeur par défaut est en base de donnée si ce n'est pas le cas, on l'ajoute, cela permet aux mises à jour des thèmes de gérer les nouvelles valeurs :)
        $themeMenus = ThemeEditorProcessor::getInstance()->getThemeMenus();
        $currentTheme = ThemeLoader::getInstance()->getCurrentTheme()->name();
        $themeConfigs = ThemeModel::getInstance()->fetchThemeConfigs($currentTheme);
        $configNames = array_column($themeConfigs, 'theme_config_name');
        $menuKeys = [];

        foreach ($themeMenus as $themeMenu) {
            $menuKey = $themeMenu->getMenuKey();

            // Warning si le menu est défini plusieurs fois
            if (in_array($menuKey, $menuKeys)) {
                Flash::send(Alert::ERROR, LangManager::translate('core.toaster.theme.editor.title'), LangManager::translate('core.toaster.theme.editor.multipleMenu', ['menukey' => $menuKey]));
            } else {
                $menuKeys[] = $menuKey;
            }

            $themeKeys = [];

            foreach ($themeMenu->getValues() as $value) {
                $key = $value->getThemeKey();

                if (in_array($key, $themeKeys)) {
                    Flash::send(Alert::ERROR, LangManager::translate('core.toaster.theme.editor.title'),  LangManager::translate('core.toaster.theme.editor.multipleMenuKey', ['key' => $key, 'menukey' => $menuKey]));
                } else {
                    $themeKeys[] = $key;
                }

                $dbKey = $menuKey ? ThemeMapper::mapConfigKey($menuKey, $key) : $key;

                if (!in_array($dbKey, $configNames)) {
                    ThemeModel::getInstance()->storeThemeConfig($dbKey, $value->getDefaultValue(), $currentTheme);
                    // Ajoute ici aussi à $themeConfigs pour éviter de devoir reload
                    $themeConfigs[] = [
                        'theme_config_name' => $dbKey,
                        'theme_config_value' => $value->getDefaultValue(),
                        'theme_config_theme' => $currentTheme,
                    ];
                }
            }
        }

        $view = new View('Core', 'Editor/themeManage');
        $view
            ->addVariableList(['themeMenus' => $themeMenus, 'themeConfigs' => $themeConfigs])
            ->setCustomPath(EnvManager::getInstance()->getValue('DIR') . "App/Package/Core/Views/Theme/Editor/themeManage.admin.view.php")
            ->setCustomTemplate(EnvManager::getInstance()->getValue('DIR') . 'App/Package/Core/Views/Theme/Editor/template.php');
        $view->view();
    }

    #[NoReturn]
    #[Link('/manage', Link::POST, [], '/cmw-admin/theme', secure: true)]
    private function adminThemeManagePost(): void
    {
        header('Content-Type: application/json');

        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.edit');

        $themeName = ThemeLoader::getInstance()->getCurrentTheme()->name();
        try {
            $newCsrfTokenId = bin2hex(random_bytes(8));
            $newCsrfToken = SecurityManager::getInstance()->getCSRFToken($newCsrfTokenId);

            $aresFiles = [];

            foreach ($_FILES as $conf => $file) {
                $aresFiles['__images__'][$conf] = true;

                if ($file['name'] !== '') {
                    $imageName = ImagesManager::convertAndUpload($file, $themeName . '/Img');

                    $currentImage = ThemeModel::getInstance()->getConfigValue($conf);
                    ImagesManager::deleteImage($themeName . "/Img/$currentImage");

                    ThemeModel::getInstance()->getInstance()->updateThemeConfig($conf, $imageName, $themeName);
                }
            }

            foreach (ThemeSettingsMapper::getFlattened($themeName) as $conf => $defaultValue) {
                if (isset($aresFiles['__images__'][$conf])) {
                    continue;
                }

                if (isset($_POST[$conf])) {
                    ThemeModel::getInstance()->updateThemeConfig($conf, $_POST[$conf], $themeName);
                }
            }

            $themeConfigs = ThemeModel::getInstance()->getInstance()->fetchThemeConfigs($themeName);
            SimpleCacheManager::storeCache($themeConfigs, 'config', 'Themes/' . $themeName);

            echo json_encode([
                'success' => true,
                'new_csrf_token' => $newCsrfToken,
                'new_csrf_token_id' => $newCsrfTokenId,
            ], JSON_THROW_ON_ERROR);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ], JSON_THROW_ON_ERROR);
            exit;
        }
    }

    #[Link('/update', Link::POST, [], '/cmw-admin/theme')]
    #[NoReturn]
    private function adminThemeUpdate(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.manage');

        if (!EnvManager::getInstance()->getValue('DEVMODE')) {
            $CoreNeedUpdate = UpdatesManager::checkNewUpdateAvailable();
            if ($CoreNeedUpdate) {
                Flash::send(Alert::ERROR, 'CORE', LangManager::translate('core.toaster.theme.updateBeforeUpdate'));
                Redirect::redirect('cmw-admin/updates/cms');
            }
        }

        $id = FilterManager::filterInputIntPost('resId');
        $actualVersion = FilterManager::filterInputStringPost('localVersion');
        $themeName = FilterManager::filterInputStringPost('themeName');
        $status = FilterManager::filterInputStringPost('status');

        $statusInt = ($status === 'test') ? 1 : 0;

        $current = PublicAPI::getData("market/resources/$id");

        if (isset($current['error'])) {
            $code = $current['error']['code'] ?? 'UNKNOWN';
            $desc = $current['error']['description']['Description']
                ?? $current['error']['description']['description']
                ?? ($current['error']['info'] ?? 'Erreur inconnue');

            Flash::send(Alert::ERROR, "Erreur ".$code, $desc);
            Redirect::redirectPreviousRoute();
        }

        $blocking = [];

        if (!empty($current['dependencies'])) {
            $missing = [];
            foreach ($current['dependencies'] as $dep) {
                if (is_null(PackageController::getPackage($dep['market_name'] ?? $dep['name'] ?? null))) {
                    $missing[] = '<b>'.($dep['market_name'] ?? $dep['name']).'</b>';
                }
            }

            if (!empty($missing)) {
                $count = count($missing);
                $list  = $count > 1
                    ? implode(', ', array_slice($missing, 0, -1)) . ' et ' . end($missing)
                    : $missing[0];

                $label = $count > 1 ? 'les packages ' : 'le package ';

                Flash::send(
                    Alert::WARNING,
                    'Packages',
                    'Veuillez installer '.$label.$list.
                    ' avant de mettre à jour <b>'.$current['market_name'].'</b> car il en a besoin pour fonctionner.'
                );
                Redirect::redirectPreviousRoute();
            }

            foreach ($current['dependencies'] as $dep) {
                // Ici tu avais ThemeLoader::getInstance()->getTheme(), je garde la logique
                $local = ThemeLoader::getInstance()->getTheme($dep['name'] ?? null);
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
                    ? implode(', ', array_slice($blocking, 0, -1)) . ' et ' . end($blocking)
                    : $blocking[0];

                $label = $count > 1 ? 'les packages ' : 'le package ';

                Flash::send(
                    Alert::WARNING,
                    'Packages',
                    "Veuillez d'abord mettre à jour {$label}{$list} avant d'actualiser <b>{$themeName}</b>."
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
                "No updates available for this theme",
            );
            Redirect::redirectPreviousRoute();
        }

        if (!Directory::delete(EnvManager::getInstance()->getValue('DIR') . "Public/Themes/$themeName")) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.theme.unableDeleteFolder')
                . EnvManager::getInstance()->getValue('DIR') . "Public/Themes/$themeName",
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
                    DownloadManager::installPackageWithLink($update['file'], 'Theme', $themeName);
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

        ThemeFileManager::getInstance()->updateThemeSettings($themeName);
        SimpleCacheManager::deleteSpecificCacheFile("config", "Themes/$themeName");

        Flash::send(
            Alert::SUCCESS,
            LangManager::translate('core.toaster.success'),
            LangManager::translate('core.theme.toasters.update.success', ['theme' => $themeName])
        );

        sleep(2);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/force/reset', Link::POST, [], '/cmw-admin/theme')]
    private function themeReset(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.manage');

        if (!CoreModel::getInstance()->updateOption('theme', 'Sampler')) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));
            Redirect::redirectPreviousRoute();
        }

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.theme.reset'));
        Redirect::redirectToHome();
    }

    #[Link('/theme/delete/:theme', Link::GET, ['theme' => '.*?'], '/cmw-admin/theme')]
    #[NoReturn]
    private function adminThemeDelete(string $theme): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.manage');

        $themeName = base64_decode($theme);
        $currentTheme = ThemeLoader::getInstance()->getCurrentTheme();

        switch (ThemeFileManager::uninstallLocalTheme($themeName)) {
            case UninstallThemeType::SUCCESS:
                if ($themeName === $currentTheme->name()) {
                    CoreModel::getInstance()->updateOption('theme', ThemeManager::$defaultThemeName);
                }
                Flash::send(Alert::SUCCESS,
                    LangManager::translate('core.toaster.success'),
                    LangManager::translate('core.toaster.theme.delete.success', ['theme' => $themeName]),
                );
                ActivatedModel::getInstance()->removeActivationByResName($themeName);
                break;
            case UninstallThemeType::ERROR_THEME_NOT_FOUND:
                Flash::send(Alert::ERROR,
                    LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.toaster.theme.delete.error.not_found', ['theme' => $themeName]),
                );
                break;
            case UninstallThemeType::ERROR_THEME_IS_DEFAULT:
                Flash::send(Alert::ERROR,
                    LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.toaster.theme.delete.error.theme_is_default'),
                );
                break;
            case UninstallThemeType::ERROR_THEME_DELETE_FILES:
                Flash::send(Alert::ERROR,
                    LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.toaster.theme.delete.error.delete_files'),
                );
                break;
        }

        Redirect::redirectPreviousRoute();
    }
}