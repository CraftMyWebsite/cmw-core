<?php

namespace CMW\Manager\Theme\Loader;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Theme\Exceptions\ThemeNotFoundException;
use CMW\Manager\Theme\IThemeConfig;
use CMW\Manager\Theme\ThemeManager;
use CMW\Model\Core\CoreModel;

class ThemeLoader extends AbstractManager
{
    public function getCurrentTheme(): IThemeConfig
    {
        $currentThemeName = ThemeManager::$defaultThemeName;
        $isInstallation = EnvManager::getInstance()->getValue('INSTALLSTEP') !== '-1';

        if (!$isInstallation) {
            $currentThemeName = CoreModel::getInstance()->fetchOption('Theme');
        }

        if (!$this::getInstance()->isLocalThemeExist($currentThemeName)) {
            (new ThemeNotFoundException($currentThemeName))->invokeErrorPage();
        }

        return $this::getInstance()->getTheme($currentThemeName);
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
     * @param string $name
     * @return bool
     */
    public function isLocalThemeExist(string $name): bool
    {
        return file_exists("Public/Themes/$name/Theme.php");
    }

}