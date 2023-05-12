<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Updater\CMSUpdaterManager;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Manager\Views\View;
use CMW\Utils\Redirect;
use CMW\Utils\Website;
use JsonException;
use ZipArchive;

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

        (new CMSUpdaterManager())->doUpdate(UpdatesManager::getCmwLatest()->version);
        Website::refresh();
    }
}