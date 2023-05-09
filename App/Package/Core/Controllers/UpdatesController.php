<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
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

    #[Link("/cms/install", Link::GET, [], "/cmw-admin/updates")]
    private function adminUpdatesInstall(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.update");

        /* todo: remove this comment ?
         * Download zip. (skip for the moment)
         * Extract zip and override files.
         * Execute updater.php file inside archive.
         * Delete zip and updater.php.
         * Update version number.
         */

        $this->downloadAndInstallUpdater();
        header("Location: ../cms");
    }

    /**
     * @return void
     * @Desc Download the updater.zip and install all the files...
     */
    protected function downloadAndInstallUpdater(): void
    {
        try {
            //First, we download the zip file and rename it with the name "updater.zip"
            $apiJson = json_decode(file_get_contents(PublicAPI::getUrl() . "/getCmwLatest"), false, 512, JSON_THROW_ON_ERROR);
            file_put_contents(EnvManager::getInstance()->getValue("DIR") . "updater.zip",
                fopen($apiJson->file_update, 'rb'));

            $archiveUpdate = new ZipArchive;
            if ($archiveUpdate->open(EnvManager::getInstance()->getValue("DIR") .'updater.zip') === TRUE) {

                $archiveUpdate->extractTo(EnvManager::getInstance()->getValue("DIR"));
                $archiveUpdate->close();

                $this->cleanInstall($apiJson->version);
            }

        } catch (JsonException) {
        }

    }

    /**
     * @param string $newVersion
     * @return void
     * @Desc Clean install and upgrade the cmw version number
     */
    protected function cleanInstall(string $newVersion): void
    {
        //Delete updater archive
        unlink(EnvManager::getInstance()->getValue("DIR") . 'updater.zip');

        //Set new version
        EnvManager::getInstance()->editValue("VERSION", $newVersion);
    }

}