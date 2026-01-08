<?php

namespace CMW\Manager\Files;

use CMW\Manager\Files\Errors\FilesStatus;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Redirect;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class FilesException extends Exception
{
    /**
     * @param string $error
     * @param int $code
     */
    public function __construct(string $error, int $code = 1)
    {
        parent::__construct($error, $code);
    }

    #[NoReturn] public static function handleFileError(FilesStatus $status): void
    {
        $message = match($status) {
            FilesStatus::ERROR_INVALID_FILE_DEFINITION => LangManager::translate('core.fileManager.error.fileDefinition'),
            FilesStatus::ERROR_FOLDER_DONT_EXIST => LangManager::translate('core.fileManager.error.folderDontExist'),
            FilesStatus::ERROR_EMPTY_FILE => LangManager::translate('core.fileManager.error.emptyFile'),
            FilesStatus::ERROR_FILE_TOO_LARGE => LangManager::translate('core.fileManager.error.tooLarge'),
            FilesStatus::ERROR_FILE_NOT_ALLOWED => LangManager::translate('core.fileManager.error.notAllowed'),
            FilesStatus::ERROR_CANT_MOVE_FILE => LangManager::translate('core.fileManager.error.move'),
            FilesStatus::ERROR_CANT_DOWNLOAD_FILE => LangManager::translate('core.fileManager.error.download'),
            FilesStatus::ERROR_CANT_CREATE_FOLDER => LangManager::translate('core.fileManager.error.createFolder'),
            FilesStatus::ERROR_INVALID_FILE_TARGET => LangManager::translate('core.fileManager.error.target'),
            FilesStatus::ERROR_INVALID_SECURE => LangManager::translate('core.fileManager.error.secure'),
            FilesStatus::ERROR_FILE_NOT_FOUND => LangManager::translate('core.fileManager.error.notFound'),
            FilesStatus::ERROR_CANT_OPEN_FILE => LangManager::translate('core.fileManager.error.cantOpen'),
            FilesStatus::ERROR_ZIP_NOT_FOUND => LangManager::translate('core.fileManager.error.zipNotFound'),
            FilesStatus::ERROR_CANT_DELETE_FILE => LangManager::translate('core.fileManager.error.deleteFile'),
        };

        Flash::send(Alert::ERROR, LangManager::translate('core.fileManager.title'), $message);
        Redirect::redirectPreviousRoute();
    }
}
