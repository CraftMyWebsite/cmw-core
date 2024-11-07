<?php

namespace CMW\Manager\Uploads;

enum ImagesConvertedStatus
{
    case ERROR_SAVING_FILE;
    case ERROR_INVALID_TARGET_FORMAT;
    case ERROR_UNSUPPORTED_CONVERSION_FORMAT;
    case ERROR_CONVERTING_IMAGE;
}