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
        $translateKey = 'core.fileManager.error.' . $status->name;
        $message = LangManager::translate($translateKey);

        Flash::send(Alert::ERROR, LangManager::translate('core.fileManager.title'), $message);
        Redirect::redirectPreviousRoute();
    }
}
