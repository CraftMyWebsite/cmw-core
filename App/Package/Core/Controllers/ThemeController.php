<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Download\DownloadManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Theme\ThemeManager;
use CMW\Manager\Uploads\ImagesException;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Directory;
use CMW\Utils\Log;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

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
        ThemeManager::getInstance()->installThemeSettings($themeName);

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
            LangManager::translate('core.toaster.Theme.installed', ['theme' => $theme['name']]));

        Redirect::redirect('cmw-admin/theme/manage');
    }

    #[Link('/manage', Link::GET, [], '/cmw-admin/theme')]
    private function adminThemeManage(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.edit');
        View::createAdminView('Core', 'Theme/themeManage')
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js', 'Admin/Resources/Vendors/Tinymce/Config/full.js', 'Admin/Resources/Vendors/PageLoader/main.js')
            ->view();
    }

    #[NoReturn]
    #[Link('/manage', Link::POST, [], '/cmw-admin/theme')]
    private function adminThemeManagePost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.edit');

        $aresFiles = [];

        // Manage files
        foreach ($_FILES as $conf => $file) {
            $aresFiles['__images__'][$conf] = true;

            // If file is empty, we don't update the config.
            if ($file['name'] !== '') {
                try {
                    $imageName = ImagesManager::upload($file, ThemeManager::getInstance()->getCurrentTheme()->name() . '/Img');
                } catch (ImagesException $e) {
                    Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                        $conf . ' => ' . $e);
                    continue;
                }

                $remoteImageValue = ThemeModel::getInstance()->getInstance()->fetchConfigValue($conf);
                $localImageValue = ThemeManager::getInstance()->getCurrentThemeConfigSetting($conf);

                if ($remoteImageValue !== $file && $remoteImageValue !== $localImageValue) {
                    ImagesManager::deleteImage(ThemeManager::getInstance()->getCurrentTheme()->name() . "/Img/$remoteImageValue");
                }

                ThemeModel::getInstance()->getInstance()->updateThemeConfig($conf, $imageName, ThemeManager::getInstance()->getCurrentTheme()->name());
            }
        }

        // Manage inputs
        foreach (ThemeManager::getInstance()->getCurrentThemeConfigSettings() as $conf => $value) {
            if (isset($aresFiles['__images__'][$conf])) {
                continue;
            }

            if (!isset($_POST[$conf]) || !empty($_POST[$conf])) {
                ThemeModel::getInstance()->getInstance()->updateThemeConfig($conf, $_POST[$conf] ?? '0', ThemeManager::getInstance()->getCurrentTheme()->name());
            }
        }

        $themeConfigs = ThemeModel::getInstance()->getInstance()->fetchThemeConfigs(ThemeManager::getInstance()->getCurrentTheme()->name());
        SimpleCacheManager::storeCache($themeConfigs, 'config', 'Themes/' . ThemeManager::getInstance()->getCurrentTheme()->name());

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.config.success'));

        Redirect::redirectPreviousRoute();
    }

    #[Link('/update/:id/:actualVersion/:themeName', Link::GET, ['id' => '[0-9]+', 'actualVersion' => '.*?', 'themeName' => '.*?'], '/cmw-admin/theme')]
    #[NoReturn]
    private function adminThemeUpdate(int $id, string $actualVersion, string $themeName): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.themes.manage');

        $updates = PublicAPI::getData("market/resources/updates/$id/$actualVersion");

        Log::debug($updates);

        // Update package

        Directory::delete(EnvManager::getInstance()->getValue('DIR') . "App/Public/Theme/$themeName");

        $lastUpdateIndex = count($updates) - 1;
        foreach ($updates as $i => $update) {
            if ($i === $lastUpdateIndex) {
                DownloadManager::installPackageWithLink($update['file'], 'Theme', $themeName);
            }
        }

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.theme.toasters.update.success', ['theme' => $themeName]));

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
}
