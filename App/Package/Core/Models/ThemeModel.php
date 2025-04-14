<?php

namespace CMW\Model\Core;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Theme\Editor\EditorType;
use CMW\Manager\Theme\ThemeManager;
use CMW\Manager\Theme\ThemeMapper;
use Exception;
use PDO;
use RuntimeException;

/**
 * Class: @ThemeModel
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class ThemeModel extends AbstractModel
{
    /**
     * @param string $menuKey
     * @param string $themeKey
     * @param string|null $themeName
     * <p>
     * If empty, we take the current active Theme
     * </p>
     * @return string|null
     * @desc Fetch config data
     */
    public function fetchConfigValue(string $menuKey, string $themeKey, string $themeName = null): ?string
    {
        if ($themeName === null) {
            $themeName = ThemeManager::getInstance()->getCurrentTheme()->name();
        }

        $type = ThemeManager::getInstance()->getEditorType($menuKey, $themeKey);
        $themeConfigNameFormatted = ThemeMapper::mapConfigKey($menuKey, $themeKey);

        $cachedValue = ThemeManager::getInstance()->getConfigValueFromCache($themeName, $themeConfigNameFormatted, $menuKey, $themeKey, $type);
        if ($cachedValue !== null) {
            return $cachedValue;
        }

        $data = ['config' => $themeConfigNameFormatted, 'theme' => $themeName];

        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT theme_config_value FROM cmw_theme_config WHERE theme_config_name = :config AND theme_config_theme = :theme');

        if ($req->execute($data)) {
            $value = $req->fetch()['theme_config_value'] ?? null;
        }

        if ($type === EditorType::IMAGE) {
            return ThemeManager::getInstance()->resolveImagePath($themeName, $value ?? null, $menuKey, $themeKey);
        }

        return $value ?? ThemeManager::getInstance()->getDefaultThemeValue($menuKey, $themeKey);
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

    /**
     * @param string $config
     * @param string|null $themeName
     * @return string|null
     * @desc Retourne simplement la valeur en DB par rapport à la clé complete
     */
    public function getConfigValue(string $config, string $themeName = null): ?string
    {
        $data = [
            'config' => $config,
            'theme' => $themeName
        ];

        if ($themeName === null) {
            $themeName = ThemeManager::getInstance()->getCurrentTheme()->name();
        }

        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT theme_config_value FROM cmw_theme_config WHERE theme_config_name = :config AND theme_config_theme = :theme');

        if ($req->execute($data)) {
            $value = $req->fetch()['theme_config_value'] ?? null;
        }

        return $value ?? '';
    }

    public function getExistingThemeConfigKeys(string $theme): array
    {
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT theme_config_name FROM cmw_theme_config WHERE theme_config_theme = :theme');

        if (!$req->execute(['theme' => $theme])) {
            return [];
        }

        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        if (!is_array($results)) {
            return [];
        }

        return array_column($results, 'theme_config_name');
    }


    public function storeThemeConfigBulk(array $configs, string $theme): bool
    {
        if (empty($configs)) return true;

        $db = DatabaseManager::getInstance();
        $db->beginTransaction();

        try {
            $stmt = $db->prepare('INSERT INTO cmw_theme_config (theme_config_name, theme_config_value, theme_config_theme) VALUES (:name, :value, :theme)');

            foreach ($configs as $config) {
                $data = [
                    'name' => $config['name'],
                    'value' => $config['value'],
                    'theme' => $theme
                ];

                if (!$stmt->execute($data)) {
                    throw new RuntimeException('Failed to insert config');
                }
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }

}
