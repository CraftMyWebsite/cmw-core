<?php

namespace CMW\Manager\Updater;

use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Utils\Directory;
use CMW\Utils\Log;
use JsonException;
use ZipArchive;

class CMSUpdaterManager extends AbstractManager
{
    private readonly string $archivePath;
    private readonly string $archiveUpdatePath;

    private readonly string $dir;

    public function __construct()
    {
        $this->dir = EnvManager::getInstance()->getValue('DIR');
        $this->archivePath = $this->dir . 'Public/Uploads/cmw.zip';
        $this->archiveUpdatePath = $this->dir . 'Public/Uploads/update.zip';
    }

    /**
     * @param array $updateData
     * @return void
     * @desc Execute the whole cms update process
     */
    public function doUpdate(array $updateData): void
    {
        if (!isset($updateData['file_update'])) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.updates.errors.nullFileUpdate'));
            return;
        }

        $updateFile = $this->downloadUpdateFile($updateData);

        if ($updateFile === false) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.updates.errors.download'));
            return;
        }

        if ($updateFile === null) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.updates.errors.nullFileUpdate'));
            return;
        }

        if (!$this->prepareArchive()) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.updates.errors.prepareArchive'));
            return;
        }

        if (!$this->deletedFiles()) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.updates.errors.deletedFiles'));
            return;
        }

        if (!$this->sqlUpdate()) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.updates.errors.sqlUpdate'));
            return;
        }

        $this->updateVersionName($updateData['value']);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.updates.success'));
    }

    /**
     * @param mixed $data
     * @return ?bool
     * @desc Download the updater file
     */
    private function downloadUpdateFile(mixed $data): ?bool
    {
        $data = $data['file_update'];

        if ($data === null) {
            return null;
        }

        if (!file_put_contents($this->archivePath, fopen($data, 'rb'))) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     * @desc Unzip maine archives
     */
    private function prepareArchive(): bool
    {
        $isInstallationFolderExist = is_dir($this->dir . 'Installation');

        $archiveUpdate = new ZipArchive;

        if ($archiveUpdate->open($this->archivePath) === TRUE) {
            $extractPath = $this->dir . 'Public/Uploads/';
            $archiveUpdate->extractTo($extractPath);
            $archiveUpdate->close();
            // Delete download archive
            unlink($this->archivePath);

            // Remove Installation files in Updated if the website doesn't use it
            if (!$isInstallationFolderExist) {
                $this->removeInstallationFolder($extractPath . 'Installation');
            }

            if ($archiveUpdate->open($this->archiveUpdatePath) === TRUE) {
                $archiveUpdate->extractTo($this->dir);
                $archiveUpdate->close();
                // Delete download archive
                unlink($this->archiveUpdatePath);
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * @param string $installationPath
     * @return void
     * @desc Remove the Installation folder if exists
     */
    private function removeInstallationFolder(string $installationPath): void
    {
        if (is_dir($installationPath)) {
            Directory::delete($installationPath);
        }
    }

    /**
     * @return bool
     * @desc Delete files, based on delete_files.json
     */
    private function deletedFiles(): bool
    {
        $filePath = $this->dir . 'Public/Uploads/delete_files.json';

        if (!file_exists($filePath)) {
            return true;
        }

        $deletedFiles = file_get_contents($filePath);

        try {
            $json = json_decode($deletedFiles, true, 512, JSON_THROW_ON_ERROR);

            foreach ($json as $file) {
                if (!unlink($this->dir . $file)) {
                    Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                        LangManager::translate('core.updates.errors.deleteFile', ['file' => $file]));
                }
            }
        } catch (JsonException) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @desc Update database if file exist
     */
    private function sqlUpdate(): bool
    {
        $filePath = $this->dir . 'Public/Uploads/sql_update.sql';

        if (!file_exists($filePath)) {
            return true;
        }

        $content = file_get_contents($filePath);

        $db = DatabaseManager::getLiteInstance();

        if (!$req = $db->query($content)) {
            return false;
        }

        $req->closeCursor();

        return true;
    }

    /**
     * @param string $version
     * @return void
     * @desc Update .env VERSION
     */
    private function updateVersionName(string $version): void
    {
        EnvManager::getInstance()->editValue('VERSION', $version);
    }

    /**
     * @param int $targetVersionId
     * @return mixed
     * @desc Return useful data
     */
    private static function getUpdateLink(int $targetVersionId): mixed
    {
        return PublicAPI::postData('/cms/update', ['current_version' => UpdatesManager::getVersion(),
            'target_version_id' => $targetVersionId]);
    }
}
