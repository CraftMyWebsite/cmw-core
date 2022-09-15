<?php

namespace CMW\Model\Core;

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Database\DatabaseManager;

/**
 * Class: @ThemeModel
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ThemeModel extends DatabaseManager
{
    public static function fetchConfigValue(string $config, string $theme = null): string
    {
        if ($theme === null) {
            $theme = ThemeController::getCurrentTheme()->getName();
        }

        $db = self::getInstance();
        $req = $db->prepare('SELECT theme_config_value FROM cmw_theme_config 
                                    WHERE theme_config_name = :config AND theme_config_theme = :theme');
        $req->execute(array("config" => $config, "theme" => $theme));

        return ($req->execute()) ? $req->fetch()["theme_config_value"] : "";
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

    public function updateThemeConfig(string $configName, string $configValue, string $theme): void
    {
        $db = self::getInstance();
        $req = $db->prepare('UPDATE cmw_theme_config SET theme_config_value = :theme_config_value 
                        WHERE theme_config_name = :theme_config_name AND theme_config_theme = :theme');

        $req->execute(array("theme_config_name" => $configName, "theme_config_value" => $configValue, "theme" => $theme));
    }

}