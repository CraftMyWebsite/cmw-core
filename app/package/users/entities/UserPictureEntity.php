<?php

namespace CMW\Entity\Users;


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
        return $this->imageName;
    }

    /**
     * @return string|null
     */
    public function getLastUpdate(): ?string
    {
        return $this->lastUpdate;
    }


}