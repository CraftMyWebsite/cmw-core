<?php

namespace CMW\Manager\Theme\Market;

use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Theme\IThemeConfigV2;
use CMW\Manager\Theme\Loader\ThemeLoader;
use CMW\Manager\Updater\UpdatesManager;

class ThemeMarketManager extends AbstractManager
{
    /**
     * @return array
     * @desc Return the list of public thèmes from our market
     */
    public function getMarketThemes(): array
    {
        if (UpdatesManager::isTestAPI()) {
            return PublicAPI::getData('market/resources/all/states/0');
        }
        return PublicAPI::getData('market/resources/filtered/0');
    }

    /**
     * @return IThemeConfigV2[]
     * @desc Return all themes local (remove thème get from the public market)
     */
    public function getLocalThemes(): array
    {
        $toReturn = [];
        $installedThemes = ThemeLoader::getInstance()->getInstalledThemes();

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
}