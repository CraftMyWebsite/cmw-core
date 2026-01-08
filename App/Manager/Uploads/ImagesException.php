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
        $translateKey = 'core.imageManager.converter.' . $status->name;
        $message = LangManager::translate($translateKey);

        Flash::send(Alert::INFO, 'Images Converter', $message);
    }

    #[NoReturn] public static function handleImageError(ImagesStatus $status): void
    {
        $translateKey = 'core.imageManager.error.' . $status->name;
        $message = LangManager::translate($translateKey);

        Flash::send(Alert::ERROR, 'Images', $message);
        Redirect::redirectPreviousRoute();
    }
}
