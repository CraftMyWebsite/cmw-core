<?php

namespace CMW\Model\Core;

use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Theme\ThemeManager;

/**
 * Class: @ThemeModel
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ThemeModel extends AbstractModel
{
    /**
     * @param string $config
     * @param string|null $themeName
     * <p>
     * If empty, we take the current active Theme
     * </p>
     * @return string|null
     * @desc Fetch config data
     */
    public function fetchConfigValue(string $config, string $themeName = null): ?string
    {
        if ($themeName === null) {
            $themeName = ThemeManager::getInstance()->getCurrentTheme()->name();
        }

        // TODO Add a toggle ?
        if (SimpleCacheManager::cacheExist('config', "Themes/$themeName")) {
            $data = SimpleCacheManager::getCache('config', "Themes/$themeName");

            foreach ($data as $conf) {
                if ($conf['theme_config_name'] === $config) {
                    return $conf['theme_config_value'] ?? "UNDEFINED_$config";
                }
            }
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT theme_config_value FROM cmw_theme_config
                                    WHERE theme_config_name = :config AND theme_config_theme = :theme');

        $req->execute(['config' => $config, 'theme' => $themeName]);

        return $req->fetch()['theme_config_value'] ?? '';
    }

    /**
     * @param string $configName
     * @param string|null $theme
     * <p>
     * If empty, we take the current active Theme
     * </p>
     * @return string|null
     * @desc Fetch config data
     */
    public function fetchImageLink(string $configName, string $theme = null): ?string
    {
        if ($theme === null) {
            $theme = ThemeManager::getInstance()->getCurrentTheme()->name();
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT theme_config_value FROM cmw_theme_config 
                                    WHERE theme_config_name = :config AND theme_config_theme = :theme');

        $req->execute(['config' => $configName, 'theme' => $theme]);

        $value = $req->fetch()['theme_config_value'] ?? '';
        $localValue = ThemeManager::getInstance()->getCurrentThemeConfigSetting($configName);

        if ($value === $localValue) {
            return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Themes/' . $theme . '/' . $localValue;
        }

        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/' . $theme . '/Img/' . $value;
    }

    /**
     * @param string $configName
     * @param string|null $theme
     * <p>
     * If empty, we take the current active Theme
     * </p>
     * @return string|null
     * @desc Fetch config data
     */
    public function fetchVideoLink(string $configName, string $theme = null): ?string
    {
        if ($theme === null) {
            $theme = ThemeManager::getInstance()->getCurrentTheme()->name();
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT theme_config_value FROM cmw_theme_config 
                                    WHERE theme_config_name = :config AND theme_config_theme = :theme');

        $req->execute(['config' => $configName, 'theme' => $theme]);

        $value = $req->fetch()['theme_config_value'] ?? '';
        $localValue = ThemeManager::getInstance()->getCurrentThemeConfigSetting($configName);

        if ($value === $localValue) {
            return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Themes/' . $theme . '/' . $localValue;
        }

        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/' . $theme . '/Videos/' . $value;
    }

    public function fetchThemeConfigs(string $theme): array
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT * FROM cmw_theme_config WHERE theme_config_theme = :theme');

        if ($req->execute(['theme' => $theme])) {
            return $req->fetchAll();
        }

        return ($req->execute()) ? $req->fetchAll() : [];
    }

    public function storeThemeConfig(string $configName, string $configValue, string $theme): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('INSERT INTO cmw_theme_config (theme_config_name, theme_config_value, theme_config_theme) 
                                    VALUES (:theme_config_name, :theme_config_value, :theme_config_theme)');
        $req->execute(['theme_config_name' => $configName,
            'theme_config_value' => $configValue,
            'theme_config_theme' => $theme]);
    }

    public function updateThemeConfig(string $configName, ?string $configValue, string $theme): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('UPDATE cmw_theme_config SET theme_config_value = :theme_config_value 
                        WHERE theme_config_name = :theme_config_name AND theme_config_theme = :theme');

        $req->execute(['theme_config_name' => $configName, 'theme_config_value' => $configValue, 'theme' => $theme]);
    }

    public function deleteThemeConfig(string $themeName): bool
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('DELETE FROM cmw_theme_config WHERE theme_config_theme = :themeName');

        return $req->execute(['themeName' => $themeName]);
    }
}
