<?php

namespace CMW\Manager\Theme;

use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Theme\Exceptions\ThemeNotFoundException;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\ThemeModel;

class ThemeManager extends AbstractManager
{
    public function getCurrentTheme(): IThemeConfig
    {
        $currentThemeName = 'Sampler';
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
     * @return \CMW\Manager\Theme\IThemeConfig|null
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
     * @return void
     */
    public function installThemeSettings(string $theme): void
    {
        $themeConfigFile = "Public/Themes/$theme/Config/config.settings.php";

        if (!file_exists($themeConfigFile)) {
            return;
        }

        $content = include $themeConfigFile;

        foreach ($content as $config => $value) {
            ThemeModel::getInstance()->getInstance()->storeThemeConfig($config, $value, $theme);
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
}
