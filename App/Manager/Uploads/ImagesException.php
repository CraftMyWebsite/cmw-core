<?php

namespace CMW\Manager\Uploads;

use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Uploads\Errors\ImagesConvertedStatus;
use CMW\Manager\Uploads\Errors\ImagesStatus;
use CMW\Utils\Redirect;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class ImagesException extends Exception
{
    /**
     * @param string $error
     * @param int $code
     */
    public function __construct(string $error, int $code = 1)
    {
        parent::__construct($error, $code);
    }

    public static function handleConverterError(ImagesConvertedStatus $status): void
    {
        $message = match($status) {
            ImagesConvertedStatus::ERROR_SAVING_FILE => LangManager::translate('core.imageManager.converter.saving'),
            ImagesConvertedStatus::ERROR_INVALID_TARGET_FORMAT => LangManager::translate('core.imageManager.converter.target'),
            ImagesConvertedStatus::ERROR_UNSUPPORTED_CONVERSION_FORMAT => LangManager::translate('core.imageManager.converter.conversion'),
            ImagesConvertedStatus::ERROR_CONVERTING_IMAGE => LangManager::translate('core.imageManager.converter.converting'),
        };

        Flash::send(Alert::INFO, 'Images Converter', $message);
    }

    #[NoReturn] public static function handleImageError(ImagesStatus $status): void
    {
        $message = match($status) {
            ImagesStatus::ERROR_INVALID_FILE_DEFINITION => LangManager::translate('core.imageManager.error.fileDefinition'),
            ImagesStatus::ERROR_FOLDER_DONT_EXIST => LangManager::translate('core.imageManager.error.folderDontExist'),
            ImagesStatus::ERROR_EMPTY_FILE => LangManager::translate('core.imageManager.error.emptyFile'),
            ImagesStatus::ERROR_FILE_TOO_LARGE => LangManager::translate('core.imageManager.error.tooLarge'),
            ImagesStatus::ERROR_FILE_NOT_ALLOWED => LangManager::translate('core.imageManager.error.notAllowed'),
            ImagesStatus::ERROR_CANT_MOVE_FILE => LangManager::translate('core.imageManager.error.move'),
            ImagesStatus::ERROR_CANT_DOWNLOAD_FILE => LangManager::translate('core.imageManager.error.download'),
            ImagesStatus::ERROR_CANT_CREATE_FOLDER => LangManager::translate('core.imageManager.error.createFolder'),
        };

        Flash::send(Alert::ERROR, 'Images', $message);
        Redirect::redirectPreviousRoute();
    }
}
