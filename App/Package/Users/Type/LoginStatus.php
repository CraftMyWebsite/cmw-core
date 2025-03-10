<?php

namespace CMW\Type\Users;

enum LoginStatus
{
    case NOT_FOUND;
    case NOT_MATCH;
    case INTERNAL_ERROR;
    case OK;
    case OK_NEED_2FA;
    case OK_ENFORCE_2FA;
    case OK_LONG_DATE;
}

