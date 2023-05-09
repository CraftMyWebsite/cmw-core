<?php

namespace CMW\Model\Core;

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;

/**
 * Class: @ThemeModel
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ThemeModel extends DatabaseManager
{
    /**
     * @param string $config
     * @param string|null $theme
     * <p>
     * If empty, we take the current active Theme
     * </p>
     * @return string|null
     * @desc Fetch config data
     */
    public static function fetchConfigValue(string $config, string $theme = null): ?string
    {
        if ($theme === null) {
            $theme = ThemeController::getCurrentTheme()->getName();
        }

        $db = self::getInstance();
        $req = $db->prepare('SELECT theme_config_value FROM cmw_theme_config 
                                    WHERE theme_config_name = :config AND theme_config_theme = :theme');

        $req->execute(array("config" => $config, "theme" => $theme));

        return $req->fetch()["theme_config_value"] ?? "";
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
    public static function fetchImageLink(string $configName, string $theme = null): ?string
    {
        if ($theme === null) {
            $theme = ThemeController::getCurrentTheme()->getName();
        }

        $db = self::getInstance();
        $req = $db->prepare('SELECT theme_config_value FROM cmw_theme_config 
                                    WHERE theme_config_name = :config AND theme_config_theme = :theme');

        $req->execute(array("config" => $configName, "theme" => $theme));

        $value = $req->fetch()["theme_config_value"] ?? "";
        $localValue = (new ThemeController())->getCurrentThemeConfigSetting($configName);

        if($value === $localValue){
            return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Themes/' . $theme . '/' . $localValue;
        }

        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Public/Uploads/' . $theme . '/Img/' . $value;
    }

    public function fetchThemeConfigs(string $theme): array
    {
        $db = self::getInstance();
        $req = $db->prepare('SELECT * FROM cmw_theme_config WHERE theme_config_theme = :theme');

        if ($req->execute(array("theme" => $theme))) {
            return $req->fetchAll();
        }

        return ($req->execute()) ? $req->fetchAll() : [];
    }

    public function storeThemeConfig(string $configName, string $configValue, string $theme): void
    {
        $db = self::getInstance();
        $req = $db->prepare('INSERT INTO cmw_theme_config (theme_config_name, theme_config_value, theme_config_theme) 
                                    VALUES (:theme_config_name, :theme_config_value, :theme_config_theme)');
        $req->execute(array("theme_config_name" => $configName,
            "theme_config_value" => $configValue,
            "theme_config_theme" => $theme));
    }

    public function updateThemeConfig(string $configName, ?string $configValue, string $theme): void
    {
        $db = self::getInstance();
        $req = $db->prepare('UPDATE cmw_theme_config SET theme_config_value = :theme_config_value 
                        WHERE theme_config_name = :theme_config_name AND theme_config_theme = :theme');

        $req->execute(array("theme_config_name" => $configName, "theme_config_value" => $configValue, "theme" => $theme));
    }

    public function deleteThemeConfig(string $themeName): void
    {
        $db = self::getInstance();
        $req = $db->prepare('DELETE FROM cmw_theme_config WHERE theme_config_theme = :themeName');

        $req->execute(array("themeName" => $themeName));
    }

}