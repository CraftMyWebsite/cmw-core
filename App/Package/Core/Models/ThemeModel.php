<?php

namespace CMW\Model\Core;

use CMW\Controller\Core\PackageController;
use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Theme\Editor\EditorType;
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
    public function fetchConfigValue(string $MenuKey, string $themeKey, string $themeName = null): ?string
    {
        //TODO Gérer les images ici !
        if ($themeName === null) {
            $themeName = ThemeManager::getInstance()->getCurrentTheme()->name();
        }

        /* TODO rework
        if (SimpleCacheManager::cacheExist('config', "Themes/$themeName")) {
            $data = SimpleCacheManager::getCache('config', "Themes/$themeName");

            foreach ($data as $conf) {
                if ($conf['theme_config_name'] === $MenuKey. '_' .$themeKey) {
                    return $conf['theme_config_value'] ?? "UNDEFINED_$MenuKey. '_' .$themeKey";
                }
            }
        }*/

        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT theme_config_value FROM cmw_theme_config
                                    WHERE theme_config_name = :config AND theme_config_theme = :theme');

        $req->execute([
            'config' => $MenuKey . '_' . $themeKey,
            'theme' => $themeName
        ]);

        $value = $req->fetch()['theme_config_value'] ?? null;

        $type = ThemeManager::getInstance()->getEditorType($MenuKey, $themeKey);

        if ($type === EditorType::IMAGE) {
            $default = $this->getDefaultThemeValue($MenuKey, $themeKey);

            if (!$value || $value === $default) {
                return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "Public/Themes/{$themeName}/{$default}";
            }

            return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . "Public/Uploads/{$themeName}/Img/{$value}";
        }

        return $value ?? $this->getDefaultThemeValue($MenuKey, $themeKey);
    }

    /**
     * Récupère la valeur par défaut d'une clé de thème depuis le fichier de configuration.
     *
     * @param string $key
     * @return mixed|null
     */
    private function getDefaultThemeValue(string $MenuKey, string $key) : string
    {
        $themeName = ThemeManager::getInstance()->getCurrentTheme()->name();
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
     * @param string $configName
     * @param string|null $theme
     * <p>
     * If empty, we take the current active Theme
     * </p>
     * @return string|null
     * @desc Fetch config data
     * @deprecated Sera supprimé en alpha-10 gérer nativement dans fetchConfigValue
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
     * @deprecated Sera supprimé en alpha-10 gérer nativement dans fetchConfigValue
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

    /**
     * @param string $theme
     * @return array
     */
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

    public function transactionalDeleteThemeConfig(string $themeName): \PDO
    {
        $db = DatabaseManager::getInstance();
        $db->beginTransaction();

        $req = $db->prepare('DELETE FROM cmw_theme_config WHERE theme_config_theme = :themeName');

        $req->execute(['themeName' => $themeName]);

        return $db;
    }
}
