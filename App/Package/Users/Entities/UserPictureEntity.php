<?php

namespace CMW\Entity\Users;

use CMW\Utils\Date;
use CMW\Model\Users\UsersModel;

class UserPictureEntity
{
    private ?int $userId;
    private ?string $image;
    private ?string $lastUpdate;

    /**
     * @param int|null $userId
     * @param string|null $image
     * @param string|null $lastUpdate
     */
    public function __construct(?int $userId, ?string $image, ?string $lastUpdate)
    {
        $this->userId = $userId;
        $this->image = $image;
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
     * @desc date
     */
    public function getLastUpdate(): ?string
    {
        if (!is_null($this->lastUpdate)) {
            return Date::formatDate($this->lastUpdate);
        }
        return (new UsersModel())->getUserById($this->userId)?->getCreated();
    }

    /**
     * @return string|null
     * @desc Get absolute path
     */
    public function getImage(): ?string
    {
        return $this->image;
    }
}
