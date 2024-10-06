<?php

namespace CMW\Interface\Users;

use CMW\Type\Users\OAuthLoginStatus;
use JetBrains\PhpStorm\NoReturn;

interface IUsersOAuth
{
    /**
     * @return string
     * @desc Return the implementation methode name, Ex: GitHub
     */
    public function methodeName(): string;

    /**
     * @return string
     * @desc Return the implementation methode identifier, Ex: github
     * @note This identifier is used to identify the oAuth methode. It must be unique. <b>Avoid spaces and specials chars.</b>
     */
    public function methodIdentifier(): string;

    /**
     * @return string
     * @desc Return the absolute link to the icon of the oAuth methode
     */
    public function methodeIconLink(): string;

    /**
     * @return void
     * @desc Redirect the client to the consent page
     */
    #[NoReturn] public function redirectToConsent(): void;

    /**
     * @return OAuthLoginStatus
     * @desc Register the user
     */
    public function register(): OAuthLoginStatus;

    /**
     * @return OAuthLoginStatus
     * @desc Login the user
     */
    public function login(): OAuthLoginStatus;

    /**
     * @return void
     * @desc Print the admin form of the oAuth implementation
     */
    public function adminForm(): void;


    /**
     * @return void
     * @desc Call when the form fields are posted
     */
    public function adminFormPost(): void;
}

