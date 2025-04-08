<?php

namespace CMW\Manager\Theme;

use CMW\Controller\Core\PackageController;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Theme\Editor\EditorMenu;
use CMW\Manager\Theme\Editor\EditorRangeOptions;
use CMW\Manager\Theme\Editor\EditorValue;
use CMW\Manager\Theme\Exceptions\ThemeNotFoundException;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Directory;
use PDO;

class ThemeManager extends AbstractManager
{
    public static string $defaultThemeName = "Sampler";

    public function defaultImageLink(): string
    {
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Assets/Img/local-theme.jpg';
    }

    public function getCurrentTheme(): IThemeConfig
    {
        $currentThemeName = self::$defaultThemeName;
        $isInstallation = EnvManager::getInstance()->getValue('INSTALLSTEP') !== '-1';

        if (!$isInstallation) {
            $currentThemeName = CoreModel::getInstance()->fetchOption('Theme');
        }

        if (!$this->isLocalThemeExist($currentThemeName)) {
            (new ThemeNotFoundException($currentThemeName))->invokeErrorPage();
        }

        return $this->getTheme($currentThemeName);
    }

    /**
     * @param string $themeName
     * @return IThemeConfig|null
     */
    public function getTheme(string $themeName): ?IThemeConfig
    {
        $namespace = 'CMW\\Theme\\' . $themeName . '\Theme';

        if (!class_exists($namespace)) {
            return null;
        }

        $classInstance = new $namespace();

        if (!is_subclass_of($classInstance, IThemeConfig::class)) {
            return null;
        }

        return $classInstance;
    }

    /**
     * @return IThemeConfig[]
     */
    public function getInstalledThemes(): array
    {
        $toReturn = [];
        $themesFolder = 'Public/Themes';
        $contentDirectory = array_diff(scandir("$themesFolder/"), ['..', '.']);
        foreach ($contentDirectory as $theme) {
            if (file_exists("$themesFolder/$theme/Theme.php") && !empty(file_get_contents("$themesFolder/$theme/Theme.php"))) {
                $toReturn[] = $this->getTheme($theme);
            }
        }

        return $toReturn;
    }

    /**
     * @return IThemeConfig[]
     * @desc Return all themes local (remove thème get from the public market)
     */
    public function getLocalThemes(): array
    {
        $toReturn = [];
        $installedThemes = $this->getInstalledThemes();

        $marketThemesName = [];

        foreach ($this->getMarketThemes() as $marketTheme):
            $marketThemesName[] = $marketTheme['name'];
        endforeach;

        foreach ($installedThemes as $installedTheme):
            if (!in_array($installedTheme->name(), $marketThemesName, true)):
                $toReturn[] = $installedTheme;
            endif;
        endforeach;

        return $toReturn;
    }

    /**
     * @return void
     */
    public function getCurrentThemeConfigFile(): void
    {
        $themeConfigFile = 'Public/Themes/' . $this->getCurrentTheme()->name() . '/Config/config.php';
        require_once $themeConfigFile;
    }

    /**
     * @return array
     * @deprecated Sera supprimé en alpha-10 remplacé par : getFlattenedThemeConfigSettings
     */
    public function getCurrentThemeConfigSettings(): array
    {
        $themeConfigFile = 'Public/Themes/' . $this->getCurrentTheme()->name() . '/Config/config.settings.php';

        if (!file_exists($themeConfigFile)) {
            return [];
        }

        $content = include $themeConfigFile;

        if (!is_array($content)) {
            return [];
        }

        return $content;
    }

    /**
     * @param string $setting
     * @return ?string
     * @Desc Return a specific local setting
     */
    public function getCurrentThemeConfigSetting(string $setting): ?string
    {
        return $this->getCurrentThemeConfigSettings()[$setting] ?? null;
    }

    /**
     * @param string $theme
     * @return bool
     */
    public function isThemeInstalled(string $theme): bool
    {
        foreach ($this->getInstalledThemes() as $installedTheme) {
            if ($theme === $installedTheme->name()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $theme
     * @desc Applique les paramètres d’un thème et écraser les valeurs existantes.
     * @return void
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

                $configKey = $menu->key . '_' . $value->themeKey;
                $defaultValue = $value->defaultValue;

                ThemeModel::getInstance()->storeThemeConfig($configKey, $defaultValue, $theme);
            }
        }
    }

    /**
     * @param string $theme
     * @desc Met à jour les paramètres d’un thème sans écraser les valeurs existantes.
     * @return void
     */
    public function updateThemeSettings(string $theme): void
    {
        $themeConfigFile = "Public/Themes/$theme/Config/config.settings.php";

        if (!file_exists($themeConfigFile)) {
            return;
        }

        $menus = include $themeConfigFile;

        // Liste des clés déjà en base
        $db = DatabaseManager::getInstance();
        $req = $db->prepare('SELECT theme_config_name FROM cmw_theme_config WHERE theme_config_theme = :theme');
        $req->execute(['theme' => $theme]);
        $existingKeys = array_column($req->fetchAll(PDO::FETCH_ASSOC), 'theme_config_name');


        foreach ($menus as $menu) {
            if (!($menu instanceof EditorMenu)) {
                continue;
            }

            foreach ($menu->values as $value) {
                if (!($value instanceof EditorValue)) {
                    continue;
                }

                $configKey = $menu->key . '_' . $value->themeKey;

                if (!in_array($configKey, $existingKeys)) {
                    ThemeModel::getInstance()->storeThemeConfig($configKey, $value->defaultValue, $theme);
                }
            }
        }
    }

    /**
     * @return array
     * @desc Return the list of public thèmes from our market
     */
    public function getMarketThemes(): array
    {
        return PublicAPI::getData('market/resources/filtered/0');
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isLocalThemeExist(string $name): bool
    {
        return file_exists("Public/Themes/$name/Theme.php");
    }

    /**
     * @param string $name
     * @return UninstallThemeType
     * @desc Completely uninstall a local theme (delete files and database)
     */
    public function uninstallLocalTheme(string $name): UninstallThemeType
    {
        if (!$this->isLocalThemeExist($name)) {
            return UninstallThemeType::ERROR_THEME_NOT_FOUND;
        }

        //Prevent default theme uninstallation
        if ($name === self::$defaultThemeName) {
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

    /*--EDITOR--*/

    /**
     * @return EditorMenu[]
     */
    public function getThemeMenus(): array {
        $themeName = ThemeManager::getInstance()->getCurrentTheme()->name();

        $configPath = EnvManager::getInstance()->getValue('DIR') . "Public/Themes/{$themeName}/Config/config.settings.php";

        if (!file_exists($configPath)) {
            return [];
        }

        $menus = include $configPath;

        return array_filter($menus, function ($menu) {
            return !isset($menu->requiredPackage) || PackageController::isInstalled($menu->requiredPackage);
        });
    }

    /**
     * @param string $menuKey
     * @param string $themeKey
     * @return string|null
     */
    public function getEditorType(string $menuKey, string $themeKey): ?string
    {
        foreach ($this->getThemeMenus() as $menu) {
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

        foreach ($this->getThemeMenus() as $menu) {
            if ($menu->key !== $menuKey) continue;
            foreach ($menu->values as $val) {
                if ($val->themeKey === $themeKey && isset($val->rangeOptions[0])) {
                    return $val->rangeOptions[0];
                }
            }
        }
        return null;
    }
}
