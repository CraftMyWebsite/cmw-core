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
    /* ADMINISTRATION */

    #[Link(path: "/", method: Link::GET, scope: "/cmw-admin/updates")]
    #[Link("/cms", Link::GET, [], "/cmw-admin/updates")]
    private function adminUpdates(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.update");

        View::createAdminView("Core", "updates")
            ->view();
    }

    #[Link("/cms/update", Link::GET, [], "/cmw-admin/updates")]
    private function adminUpdatesInstall(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.update");

        // We get all the new versions id.
        $targetVersions = PublicAPI::postData('cms/update', ['current_version' => UpdatesManager::getVersion()]);

        foreach ($targetVersions as $key => $targetVersion) {
            (new CMSUpdaterManager())->doUpdate($targetVersion);
        }

        Redirect::redirectPreviousRoute();
    }
}