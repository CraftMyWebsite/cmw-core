<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
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
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Theme\ThemeManager;
use CMW\Manager\Theme\ThemeSettingsMapper;
use CMW\Manager\Theme\UninstallThemeType;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
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

        $currentTheme = ThemeManager::getInstance()->getCurrentTheme();
        $installedThemes = ThemeManager::getInstance()->getInstalledThemes();
        $themesList = ThemeManager::getInstance()->getMarketThemes();

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

        $currentTheme = ThemeManager::getInstance()->getCurrentTheme();
        $installedThemes = ThemeManager::getInstance()->getInstalledThemes();
        $themesList = ThemeManager::getInstance()->getMarketThemes();

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

        $themeName = ThemeManager::getInstance()->getCurrentTheme()->name();
        ThemeModel::getInstance()->getInstance()->deleteThemeConfig($themeName);
        ThemeManager::getInstance()->updateThemeSettings($themeName);

        $themeConfigs = ThemeModel::getInstance()->getInstance()->fetchThemeConfigs($themeName);
        SimpleCacheManager::storeCache($themeConfigs, 'config', 'Themes/' . $themeName);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.theme.regenerate'));

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/install/:id', Link::GET, ['id' => '[0-9]+'], '/cmw-admin/theme')]
    private function adminThemeInstallation(int $id): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.manage');

        if (!EnvManager::getInstance()->getValue('DEVMODE')) {
            $CoreNeedUpdate = UpdatesManager::checkNewUpdateAvailable();
            if ($CoreNeedUpdate) {
                Flash::send(Alert::ERROR, 'CORE', LangManager::translate('core.toaster.theme.updateBeforeInstall'));
                Redirect::redirect('cmw-admin/updates/cms');
            }
        }

        $theme = PublicAPI::putData("market/resources/install/$id");

        if (empty($theme)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError') . ' (API)');
            Redirect::redirectPreviousRoute();
        }

        if (!DownloadManager::installPackageWithLink($theme['file'], 'Theme', $theme['name'])) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.downloads.errors.internalError',
                    ['name' => $theme['name'], 'version' => $theme['version_name']]));
            Redirect::redirectPreviousRoute();
        }

        // Install Theme settings
        ThemeManager::getInstance()->installThemeSettings($theme['name']);
        CoreModel::getInstance()->updateOption('theme', $theme['name']);

        $themeConfigs = ThemeModel::getInstance()->getInstance()->fetchThemeConfigs($theme['name']);
        SimpleCacheManager::storeCache($themeConfigs, 'config', 'Themes/' . $theme['name']);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.theme.installed', ['theme' => $theme['name']]));

        Redirect::redirect('cmw-admin/theme/manage');
    }

    #[Link('/manage', Link::GET, [], '/cmw-admin/theme')]
    private function adminThemeManage(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.edit');

        //Vérifie si la valeur par défaut est en base de donnée si ce n'est pas le cas, on l'ajoute, cela permet aux mises à jour des thèmes de gérer les nouvelles valeurs :)
        $themeMenus = ThemeManager::getInstance()->getThemeMenus();
        $currentTheme = ThemeManager::getInstance()->getCurrentTheme()->name();
        $themeConfigs = ThemeModel::getInstance()->fetchThemeConfigs($currentTheme);
        $configNames = array_column($themeConfigs, 'theme_config_name');
        $menuKeys = [];

        foreach ($themeMenus as $themeMenu) {
            $menuKey = $themeMenu->getMenuKey();

            // Warning si le menu est défini plusieurs fois
            if (in_array($menuKey, $menuKeys)) {
                Flash::send(Alert::ERROR, 'Editor - DEV', 'Attention le menu <b>' . $menuKey . '</b> est défini plusieurs fois !');
            } else {
                $menuKeys[] = $menuKey;
            }

            $themeKeys = [];

            foreach ($themeMenu->getValues() as $value) {
                $key = $value->getThemeKey();

                if (in_array($key, $themeKeys)) {
                    Flash::send(Alert::ERROR, 'Editor - DEV', 'Attention la clé <b>' . $key . '</b> est présente plusieurs fois dans le menu <b>' . $menuKey . '</b> !');
                } else {
                    $themeKeys[] = $key;
                }

                $dbKey = $menuKey ? $menuKey . '_' . $key : $key;

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

        $view = new View();
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

        $themeName = ThemeManager::getInstance()->getCurrentTheme()->name();
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

    #[Link('/update/:id/:actualVersion/:themeName', Link::GET, ['id' => '[0-9]+', 'actualVersion' => '.*?', 'themeName' => '.*?'], '/cmw-admin/theme')]
    #[NoReturn]
    private function adminThemeUpdate(int $id, string $actualVersion, string $themeName): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.manage');

        if (!EnvManager::getInstance()->getValue('DEVMODE')) {
            $CoreNeedUpdate = UpdatesManager::checkNewUpdateAvailable();
            if ($CoreNeedUpdate) {
                Flash::send(Alert::ERROR, 'CORE', LangManager::translate('core.toaster.theme.updateBeforeUpdate'));
                Redirect::redirect('cmw-admin/updates/cms');
            }
        }

        $updates = PublicAPI::getData("market/resources/updates/$id/$actualVersion");

        if (Directory::delete(EnvManager::getInstance()->getValue('DIR') . "Public/Theme/$themeName")) {
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
                    if (!DownloadManager::installPackageWithLink($update['file'], 'Theme', $themeName)) {
                        Flash::send(
                            Alert::ERROR,
                            LangManager::translate('core.toaster.error'),
                            LangManager::translate('core.toaster.theme.unableUpdate') . $update['title'],
                        );
                        Redirect::redirectPreviousRoute();
                    }
                }
            }

            //Une fois que toutes les mises à jour sont terminé, on rajoute les nouvelles configs :
            ThemeManager::getInstance()->updateThemeSettings($themeName);

            SimpleCacheManager::deleteSpecificCacheFile("config", "Themes/$themeName");

            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate('core.theme.toasters.update.success', ['theme' => $themeName]));
        } else {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.theme.unableDeleteFolder') . EnvManager::getInstance()->getValue('DIR') . "Public/Theme/$themeName",
            );
        }

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
        $currentTheme = ThemeManager::getInstance()->getCurrentTheme();

        switch (ThemeManager::getInstance()->uninstallLocalTheme($themeName)) {
            case UninstallThemeType::SUCCESS:
                if ($themeName === $currentTheme->name()) {
                    CoreModel::getInstance()->updateOption('theme', ThemeManager::$defaultThemeName);
                }
                Flash::send(Alert::SUCCESS,
                    LangManager::translate('core.toaster.success'),
                    LangManager::translate('core.toaster.theme.delete.success', ['theme' => $themeName]),
                );
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