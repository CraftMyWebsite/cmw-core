<?php

namespace CMW\Type\Users;

enum OAuthLoginStatus
{
    case SUCCESS_REGISTER;
    case SUCCESS_LOGIN;
    case INVALID_CODE;
    case INVALID_TOKEN;

    case INVALID_USER_INFO;
    case EMAIL_ALREADY_EXIST;
    case UNABLE_TO_CREATE_USER;
    case UNABLE_TO_CREATE_OAUTH_USER;
}