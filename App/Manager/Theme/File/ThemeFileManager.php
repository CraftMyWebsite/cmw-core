<?php

namespace CMW\Manager\Theme\File;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Theme\Config\ThemeMapper;
use CMW\Manager\Theme\Editor\Entities\EditorMenu;
use CMW\Manager\Theme\Editor\Entities\EditorValue;
use CMW\Manager\Theme\Loader\ThemeLoader;
use CMW\Manager\Theme\ThemeManager;
use CMW\Manager\Theme\UninstallThemeType;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Directory;

class ThemeFileManager extends AbstractManager
{
    public function defaultImageLink(): string
    {
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Assets/Img/local-theme.jpg';
    }

    /**
     * @param string $theme
     * @return void
     * @desc Apply the settings of a theme and overwrite existing values.
     */
    public function installThemeSettings(string $theme): void
    {
        $themeConfigFile = "Public/Themes/$theme/Config/config.settings.php";

        if (!file_exists($themeConfigFile)) {
            return;
        }

        $menus = include $themeConfigFile;

        foreach ($menus as $menu) {
            if (!($menu instanceof EditorMenu)) {
                continue;
            }

            foreach ($menu->values as $value) {
                if (!($value instanceof EditorValue)) {
                    continue;
                }

                $configKey = ThemeMapper::mapConfigKey($menu->key, $value->themeKey);
                $defaultValue = $value->defaultValue;

                ThemeModel::getInstance()->storeThemeConfig($configKey, $defaultValue, $theme);
            }
        }
    }

    /**
     * @param string $theme
     * @return void
     * @desc Update the settings of a theme without overwriting existing values.
     */
    public function updateThemeSettings(string $theme): void
    {
        $themeConfigFile = "Public/Themes/$theme/Config/config.settings.php";

        if (!file_exists($themeConfigFile)) {
            return;
        }

        $menus = include $themeConfigFile;

        $existingKeys = ThemeModel::getInstance()->getExistingThemeConfigKeys($theme);


        foreach ($menus as $menu) {
            if (!($menu instanceof EditorMenu)) {
                continue;
            }

            foreach ($menu->values as $value) {
                if (!($value instanceof EditorValue)) {
                    continue;
                }

                $configKey = ThemeMapper::mapConfigKey($menu->key, $value->themeKey);

                $newConfigs = [];

                if (!in_array($configKey, $existingKeys, true)) {
                    $newConfigs[] = [
                        'name' => $configKey,
                        'value' => $value->defaultValue
                    ];
                }
            }
        }
        if (!empty($newConfigs)) {
            ThemeModel::getInstance()->storeThemeConfigBulk($newConfigs, $theme);
        }
    }

    /**
     * @param string $name
     * @return UninstallThemeType
     * @desc Completely uninstall a local theme (delete files and database)
     */
    public static function uninstallLocalTheme(string $name): UninstallThemeType
    {
        if (!ThemeLoader::getInstance()->isLocalThemeExist($name)) {
            return UninstallThemeType::ERROR_THEME_NOT_FOUND;
        }

        //Prevent default theme uninstallation
        if ($name === ThemeManager::$defaultThemeName) {
            return UninstallThemeType::ERROR_THEME_IS_DEFAULT;
        }

        // Uninstall DB
        $configPdo = ThemeModel::getInstance()->getInstance()->transactionalDeleteThemeConfig($name);

        // Uninstall files
        if (!Directory::delete(EnvManager::getInstance()->getValue('DIR') . "Public/Themes/$name")) {
            $configPdo->rollBack();
            return UninstallThemeType::ERROR_THEME_DELETE_FILES;
        }

        //If all is good, we commit the transaction
        $configPdo->commit();

        return UninstallThemeType::SUCCESS;
    }
}