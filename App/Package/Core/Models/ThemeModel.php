<?php

namespace CMW\Model\Core;

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractModel;

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
    public static function fetchConfigValue(string $config, string $themeName = null): ?string
    {
        if ($themeName === null) {
            $themeName = ThemeController::getCurrentTheme()->getName();
        }

        //TODO Add a toggle ?
        if (SimpleCacheManager::cacheExist('config', "Themes/$themeName")){
            $data = SimpleCacheManager::getCache('config', "Themes/$themeName");

            foreach ($data as $conf) {
                if ($conf['theme_config_name'] === $config){
                    return $conf['theme_config_value'] ?? "UNDEFINED_$config";
                }
           }
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT theme_config_value FROM cmw_theme_config
                                    WHERE theme_config_name = :config AND theme_config_theme = :theme');

        $req->execute(array("config" => $config, "theme" => $themeName));

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

        $db = DatabaseManager::getInstance();
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
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT * FROM cmw_theme_config WHERE theme_config_theme = :theme');

        if ($req->execute(array("theme" => $theme))) {
            return $req->fetchAll();
        }

        return ($req->execute()) ? $req->fetchAll() : [];
    }

    public function storeThemeConfig(string $configName, string $configValue, string $theme): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('INSERT INTO cmw_theme_config (theme_config_name, theme_config_value, theme_config_theme) 
                                    VALUES (:theme_config_name, :theme_config_value, :theme_config_theme)');
        $req->execute(array("theme_config_name" => $configName,
            "theme_config_value" => $configValue,
            "theme_config_theme" => $theme));
    }

    public function updateThemeConfig(string $configName, ?string $configValue, string $theme): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('UPDATE cmw_theme_config SET theme_config_value = :theme_config_value 
                        WHERE theme_config_name = :theme_config_name AND theme_config_theme = :theme');

        $req->execute(array("theme_config_name" => $configName, "theme_config_value" => $configValue, "theme" => $theme));
    }

    public function deleteThemeConfig(string $themeName): void
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('DELETE FROM cmw_theme_config WHERE theme_config_theme = :themeName');

        $req->execute(array("themeName" => $themeName));
    }

    public function initConfigCache(string $themeName): void
    {
        $sql = "SELECT * FROM cmw_theme_config WHERE theme_config_theme = :theme";
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['theme' => $themeName])){
            return;
        }

        $res = $req->fetchAll();

        if (!$res){
            return;
        }

        if (SimpleCacheManager::cacheExist('config', "Themes/$themeName")){
            SimpleCacheManager::deleteSpecificCacheFile('config', "Themes/$themeName");
        }

        SimpleCacheManager::storeCache($res, 'config', "Themes/$themeName");
    }

}