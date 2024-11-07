<?php

namespace CMW\Manager\Uploads;

use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
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
            ImagesConvertedStatus::ERROR_SAVING_FILE => 'Erreur lors de la sauvegarde de l\'image.',
            ImagesConvertedStatus::ERROR_INVALID_TARGET_FORMAT => 'Format cible invalide. Original conservé !',
            ImagesConvertedStatus::ERROR_UNSUPPORTED_CONVERSION_FORMAT => 'Format de conversion non supporté, Original conservé !',
            ImagesConvertedStatus::ERROR_CONVERTING_IMAGE => 'Erreur lors de la conversion de l\'image. Original conservé !',
        };

        Flash::send(Alert::INFO, 'Images Converter', $message);
    }

    #[NoReturn] public static function handleImageError(ImagesStatus $status): void
    {
        $message = match($status) {
            ImagesStatus::ERROR_INVALID_FILE_DEFINITION => 'Cette extension n\'est pas prise en charge !',
            ImagesStatus::ERROR_FOLDER_DONT_EXIST => 'Dossier cible introuvable',
            ImagesStatus::ERROR_EMPTY_FILE => 'Aucune image envoyé !',
            ImagesStatus::ERROR_FILE_TOO_LARGE => 'Image trop volumineuse !',
            ImagesStatus::ERROR_FILE_NOT_ALLOWED => 'Type de document non autorisé !',
            ImagesStatus::ERROR_CANT_MOVE_FILE => 'Impossible de déplacer l\'image.',
            ImagesStatus::ERROR_CANT_DOWNLOAD_FILE => 'Impossible télécharger l\'image.',
            ImagesStatus::ERROR_CANT_CREATE_FOLDER => 'Impossible de créer le dossier cible, problème de permission sur Public/Uploads',
        };

        Flash::send(Alert::ERROR, 'Images', $message);
        Redirect::redirectPreviousRoute();
    }
}
