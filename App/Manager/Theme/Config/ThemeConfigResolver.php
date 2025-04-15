<?php

namespace CMW\Manager\Theme\Config;

use CMW\Controller\Core\PackageController;
use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Theme\Editor\Entities\EditorRangeOptions;
use CMW\Manager\Theme\Editor\Entities\EditorType;
use CMW\Manager\Theme\Editor\ThemeEditorProcessor;
use CMW\Manager\Theme\Loader\ThemeLoader;

class ThemeConfigResolver extends AbstractManager
{
    public function getConfigValueFromCache(string $themeName, string $themeConfigNameFormatted, string $menuKey, string $themeKey, ?string $type): ?string
    {
        $data = SimpleCacheManager::getCache('config', "Themes/$themeName");

        if (is_null($data)) {
            return null;
        }

        foreach ($data as $conf) {
            if ($conf['theme_config_name'] === $themeConfigNameFormatted) {
                if ($type === EditorType::IMAGE) {
                    $default = $this->getDefaultThemeValue($menuKey, $themeKey);
                    if (!$conf['theme_config_value'] || $conf['theme_config_value'] === $default) {
                        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "Public/Themes/{$themeName}/{$default}";
                    }
                    return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "Public/Uploads/{$themeName}/Img/{$conf['theme_config_value']}";
                }
                return $conf['theme_config_value'] ?? "UNDEFINED_$themeConfigNameFormatted";
            }
        }

        return null;
    }

    /**
     * Récupère la valeur par défaut d'une clé de thème depuis le fichier de configuration.
     *
     * @param string $MenuKey
     * @param string $key
     * @return string
     */
    public function getDefaultThemeValue(string $MenuKey, string $key): string
    {
        $themeName = ThemeLoader::getInstance()->getCurrentTheme()->name();
        $configPath = EnvManager::getInstance()->getValue('DIR') . "Public/Themes/{$themeName}/Config/config.settings.php";

        if (!file_exists($configPath)) {
            return '';
        }

        $menus = include $configPath;

        foreach ($menus as $menu) {
            if (isset($menu->requiredPackage) && !PackageController::isInstalled($menu->requiredPackage)) {
                continue;
            }
            if ($menu->key === $MenuKey) {
                foreach ($menu->values as $value) {
                    if ($value->themeKey === $key) {
                        return $value->defaultValue;
                    }
                }
            }

        }
        return '';
    }

    /**
     * @param string $menuKey
     * @param string $themeKey
     * @return string|null
     */
    public function getEditorType(string $menuKey, string $themeKey): ?string
    {
        foreach (ThemeEditorProcessor::getInstance()->getThemeMenus() as $menu) {
            if ($menu->getMenuKey() === $menuKey) {
                foreach ($menu->values as $value) {
                    if ($value->themeKey === $themeKey) {
                        return $value->type;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param string $menuKey
     * @param string $themeKey
     * @return EditorRangeOptions|null
     */
    public function getEditorRangeOptions(string $menuKey, string $themeKey): ?EditorRangeOptions
    {

        foreach (ThemeEditorProcessor::getInstance()->getThemeMenus() as $menu) {
            if ($menu->key !== $menuKey) continue;
            foreach ($menu->values as $val) {
                if ($val->themeKey === $themeKey && isset($val->rangeOptions[0])) {
                    return $val->rangeOptions[0];
                }
            }
        }
        return null;
    }

    public function resolveImagePath(string $themeName, ?string $value, string $menuKey, string $themeKey): string
    {
        $default = $this->getDefaultThemeValue($menuKey, $themeKey);
        $subfolder = EnvManager::getInstance()->getValue('PATH_SUBFOLDER');

        if (!$value || $value === $default) {
            return $subfolder . "Public/Themes/{$themeName}/{$default}";
        }

        return $subfolder . "Public/Uploads/{$themeName}/Img/{$value}";
    }
}