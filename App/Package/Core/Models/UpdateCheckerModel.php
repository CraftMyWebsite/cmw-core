<?php

namespace CMW\Model\Core;

use CMW\Controller\Core\PackageController;
use CMW\Entity\Core\UpdateCheckerEntity;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Theme\Loader\ThemeLoader;

/**
 * Class: @UpdateCheckerModel
 * @package Core
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/models
 */
class UpdateCheckerModel extends AbstractModel
{
    /**
     * @return array
     * @desc Return all the online ressources
     */
    public function getMarketResourcesOnline(): array
    {
        return PublicAPI::getData('market/resources/latest/online');
    }

    /**
     * @return UpdateCheckerEntity[]
     * @desc Return the list of public thèmes from our market
     */
    public function getOutdatedResources(): array
    {
        $marketResources = $this->getMarketResourcesOnline();

        $marketByName = [];
        foreach ($marketResources as $r) {
            $name = $r['name'] ?? null;
            if ($name) {
                $marketByName[$name] = $r;
            }
        }

        $outdated = [];

        foreach (ThemeLoader::getInstance()->getInstalledThemes() as $theme) {
            $name = $theme->name();
            $localVersion = (string) $theme->version();

            if (!isset($marketByName[$name])) {
                continue;
            }

            $remoteVersion = (string) ($marketByName[$name]['version_name'] ?? '');

            if ($remoteVersion !== '' && $localVersion !== $remoteVersion) {
                $outdated[] = new UpdateCheckerEntity(
                    name: $name,
                    marketName: $marketByName[$name]['market_name'] ?? null,
                    type: 'theme',
                    localVersion: $localVersion,
                    remoteVersion: $remoteVersion,
                    dateRelease: $marketByName[$name]['date_release'] ?? null,
                    versionId: isset($marketByName[$name]['version_id']) ? (int) $marketByName[$name]['version_id'] : null
                );
            }
        }

        foreach (PackageController::getInstalledPackages() as $package) {
            $name = $package->name();
            $localVersion = (string) $package->version();

            if (!isset($marketByName[$name])) {
                continue;
            }

            $remoteVersion = (string) ($marketByName[$name]['version_name'] ?? '');

            if ($remoteVersion !== '' && $localVersion !== $remoteVersion) {
                $outdated[] = new UpdateCheckerEntity(
                    name: $name,
                    marketName: $marketByName[$name]['market_name'] ?? null,
                    type: 'package',
                    localVersion: $localVersion,
                    remoteVersion: $remoteVersion,
                    dateRelease: $marketByName[$name]['date_release'] ?? null,
                    versionId: isset($marketByName[$name]['version_id']) ? (int) $marketByName[$name]['version_id'] : null
                );
            }
        }

        return $outdated;
    }
}
