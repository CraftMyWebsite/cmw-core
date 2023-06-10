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

class UpdatesController extends AbstractController
{
    /**
     * @return array
     * @desc Return the list of public changelog for last version
     */
    public static function getLatest(): array {
        return PublicAPI::getData("cms/getLatest");
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

        $latestVersion = self::getLatest();
        $latestVersionChangelogGroup = self::groupBy("type", $latestVersion['changelog']);

        View::createAdminView("Core", "updates")
            ->addVariableList(["latestVersion" => $latestVersion, "latestVersionChangelogGroup" => $latestVersionChangelogGroup])
            ->view();
    }

    #[Link("/cms/update", Link::GET, [], "/cmw-admin/updates")]
    private function adminUpdatesInstall(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.update");

        // We get all the new versions id.
        $targetVersions = PublicAPI::postData('cms/update', ['current_version' => UpdatesManager::getVersion()]);

        foreach ($targetVersions as $targetVersion) {
            (new CMSUpdaterManager())->doUpdate($targetVersion['id']);
        }

        Redirect::redirectPreviousRoute();
    }
}