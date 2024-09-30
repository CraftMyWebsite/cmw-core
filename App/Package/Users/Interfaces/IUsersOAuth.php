<?php

namespace CMW\Interface\Users;

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
     * @return bool
     * @desc Register the user, and return the oAuth id
     */
    public function register(): bool;

    /**
     * @return bool
     * @desc Login the user, and return the oAuth id
     */
    public function login(): bool;
}

