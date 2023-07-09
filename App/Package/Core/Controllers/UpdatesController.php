<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Updater\CMSUpdaterManager;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Manager\Views\View;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

class UpdatesController extends AbstractController
{
    /**
     * @param string $latestVersionName
     * @return array
     * @desc Return the list of public changelog for previous version
     */
    public static function getPrevious(string $latestVersionName): array {
        return PublicAPI::getData("cms/getPrevious&version=" . $latestVersionName);
    }

    /**
     * Function that groups an array of associative arrays by some key.
     *
     * @param {String} $key Property to sort by.
     * @param {Array} $data Array that stores multiple associative arrays.
     */
    public static function groupBy($key, $data): array {
        $result = array();

        foreach($data as $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]][] = $val;
            }else{
                $result[""][] = $val;
            }
        }

        return $result;
    }

    /* ADMINISTRATION */

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/updates")]
    #[Link("/cms", Link::GET, [], "/cmw-admin/updates")]
    private function adminUpdates(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.update");

        $latestVersion = UpdatesManager::getCmwLatest();
        $latestVersionChangelogGroup = self::groupBy("type", $latestVersion['changelog']);
        $previousVersions = self::getPrevious($latestVersion['value']);
        $currentVersion = UpdatesManager::getVersion();

        View::createAdminView("Core", "Update/updates")
            ->addVariableList(["latestVersion" => $latestVersion, "latestVersionChangelogGroup" =>
                $latestVersionChangelogGroup, "previousVersions" => $previousVersions,
                "currentVersion" => $currentVersion])
            ->view();
    }

    #[NoReturn] #[Link("/cms/update", Link::GET, [], "/cmw-admin/updates")]
    private function adminUpdatesInstall(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.update");

        // We get all the new version id.
        $targetVersions = PublicAPI::postData('cms/update', ['current_version' => UpdatesManager::getVersion()]);

        foreach ($targetVersions as $targetVersion) {
            (new CMSUpdaterManager())->doUpdate($targetVersion['id']);
        }

        Redirect::redirectPreviousRoute();
    }
}