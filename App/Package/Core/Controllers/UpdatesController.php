<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Cache\SimpleCacheManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Updater\CMSUpdaterManager;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Manager\Views\View;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @UpdatesController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 0.0.1
 */
class UpdatesController extends AbstractController
{
    /**
     * @param string $latestVersionName
     * @return array
     * @desc Return the list of public changelog for previous version
     */
    public static function getPrevious(string $latestVersionName): array
    {
        return PublicAPI::getData("cms/previous/$latestVersionName");
    }

    /**
     * Function that groups an array of associative arrays by some key.
     *
     * @param {String} $key Property to sort by.
     * @param {Array} $data Array that stores multiple associative arrays.
     */
    public static function groupBy($key, $data): array
    {
        $result = [];

        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[''][] = $val;
            }
        }

        return $result;
    }

    /* ADMINISTRATION */

    #[Link(path: '/', method: Link::GET, scope: '/cmw-admin/updates')]
    #[Link('/cms', Link::GET, [], '/cmw-admin/updates')]
    private function adminUpdates(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.update');

        $latestVersion = UpdatesManager::getCmwLatest();
        $latestVersionChangelogGroup = self::groupBy('type', $latestVersion['changelog']);
        $previousVersions = self::getPrevious($latestVersion['value']);
        $currentVersion = UpdatesManager::getVersion();

        View::createAdminView('Core', 'Update/updates')
            ->addVariableList(['latestVersion' => $latestVersion, 'latestVersionChangelogGroup' =>
                $latestVersionChangelogGroup, 'previousVersions' => $previousVersions,
                'currentVersion' => $currentVersion])
            ->view();
    }

    #[NoReturn]
    #[Link('/cms/update', Link::GET, [], '/cmw-admin/updates')]
    private function adminUpdatesInstall(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.update');

        $currentVersion = UpdatesManager::getVersion();

        if ($currentVersion === 'DEV') {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.updates.errors.devVersion'));
            Redirect::redirectPreviousRoute();
        }

        // We get all the new version id.
        $versions = PublicAPI::postData('cms/update', ['current_version' => UpdatesManager::getVersion()]);

        foreach ($versions as $version) {
            CMSUpdaterManager::getInstance()->doUpdate($version);
        }

        SimpleCacheManager::deleteAllFiles();

        Redirect::redirectPreviousRoute();
    }
}
