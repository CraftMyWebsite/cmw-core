<?php

namespace CMW\Manager\Theme;

enum UninstallThemeType
{
    case  SUCCESS;
    case ERROR_THEME_NOT_FOUND;
    case ERROR_THEME_IS_DEFAULT;
    case ERROR_THEME_DELETE_DATABASE;
    case ERROR_THEME_DELETE_FILES;
}