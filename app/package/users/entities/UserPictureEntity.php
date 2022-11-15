<?php

namespace CMW\Entity\Users;


use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\Utils;

class UserPictureEntity
{
    private ?int $userId;
    private ?string $imageName;
    private ?string $lastUpdate;

    /**
     * @param int|null $userId
     * @param string|null $imageName
     * @param string|null $lastUpdate
     */
    public function __construct(?int $userId, ?string $imageName, ?string $lastUpdate)
    {
        $this->userId = $userId;
        $this->imageName = $imageName;
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return string|null
     */
    public function getImageName(): ?string
    {
        if(!is_file(Utils::getEnv()->getValue("DIR") . "public/uploads/users/" . $this->imageName))
        {
            return "default/" . UsersSettingsModel::getSetting("defaultImage");
        }
        return $this->imageName;
    }

    /**
     * @return string|null
     */
    public function getLastUpdate(): ?string
    {
        return $this->lastUpdate;
    }

    /**
     * @return string|null
     * @desc Get absolute path
     */
    public function getImageLink(): ?string
    {
        return Utils::getEnv()->getValue("PATH_SUBFOLDER") . "public/uploads/users/" . $this->imageName;
    }

}