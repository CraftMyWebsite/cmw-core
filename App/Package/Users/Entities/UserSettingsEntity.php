<?php

namespace CMW\Entity\Users;


use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\Redirect;

class UserSettingsEntity
{
    private string $defaultImage;
    private int $profilePageStatus;
    private int $resetPasswordMethod;


    public function __construct()
    {
        $this->defaultImage = UsersSettingsModel::getSetting('defaultImage');
        $this->profilePageStatus = (int)UsersSettingsModel::getSetting('profilePage');
        $this->resetPasswordMethod = (int)UsersSettingsModel::getSetting('resetPasswordMethod');
    }

    /**
     * @return string
     */
    public function getDefaultImage(): string
    {
        return $this->defaultImage;
    }

    /**
     * @return int
     */
    public function getProfilePageStatus(): int
    {
        return $this->profilePageStatus;
    }

    /**
     * @return int
     */
    public function getResetPasswordMethod(): int
    {
        return $this->resetPasswordMethod;
    }

    /**
     * @return bool
     * @desc Check if the profile page is enable, based on usersSettings page
     */
    public function isProfilePageEnabled(): bool
    {
        return $this->profilePageStatus !== 2;
    }

    /**
     * @param string $pseudo
     * @return void
     * @Desc Redirect to the profile page
     */
    public function goToProfilePage(string $pseudo = ''): void
    {
        match ($this->profilePageStatus){
            0 => Redirect::redirect('profile'),
            1 => Redirect::redirect("profile/$pseudo"),
            2 => Redirect::redirectToHome()
        };
    }
}