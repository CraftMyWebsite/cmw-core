<?php

namespace CMW\Manager\Uploads;

enum ImagesStatus
{
    case ERROR_INVALID_FILE_DEFINITION;
    case ERROR_FOLDER_DONT_EXIST;
    case ERROR_EMPTY_FILE;
    case ERROR_FILE_TOO_LARGE;
    case ERROR_FILE_NOT_ALLOWED;
    case ERROR_CANT_MOVE_FILE;
    case ERROR_CANT_DOWNLOAD_FILE;
    case ERROR_CANT_CREATE_FOLDER;
}