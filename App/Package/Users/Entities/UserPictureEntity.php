<?php

namespace CMW\Entity\Users;


use CMW\Controller\Core\CoreController;
use CMW\Controller\Users\UsersSettingsController;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\EnvManager;

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
        if(!is_file(EnvManager::getInstance()->getValue("DIR") . "Public/Uploads/Users/" . $this->imageName))
        {
            return "Default/" . UsersSettingsModel::getSetting("defaultImage");
        }
        return $this->imageName;
    }

    /**
     * @return string|null
     * @desc date
     */
    public function getLastUpdate(): ?string
    {
        if (!is_null($this->lastUpdate))
        {
            return CoreController::formatDate($this->lastUpdate);
        }
        return (new UsersModel())->getUserById($this->userId)?->getCreated();
    }

    /**
     * @return string|null
     * @desc Get absolute path
     */
    public function getImageLink(): ?string
    {
        return EnvManager::getInstance()->getValue("PATH_SUBFOLDER") . "Public/Uploads/Users/" . $this->imageName;
    }

    /**
     * @return string
     */
    public function getDefaultPictureLink(): string
    {
        return UsersSettingsController::getDefaultImageLink();
    }

}