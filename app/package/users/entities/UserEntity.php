<?php

namespace CMW\Entity\Users;

use CMW\Entity\Roles\RoleEntity;

class UserEntity
{

    private int $userId;
    private string $userMail;
    private string $userUsername;
    private string $userFirstName;
    private string $userLastName;
    private int $userState;
    private string $userLastConnection;
    /** @var \CMW\Entity\Roles\RoleEntity|\CMW\Entity\Roles\RoleEntity[] $userRoles */
    private array $userRoles;
    private string $userCreated;
    private string $userUpdated;

    /**
     * @param int $userId
     * @param string $userMail
     * @param string $userUsername
     * @param string $userFirstName
     * @param string $userLastName
     * @param int $userState
     * @param string $userLastConnection
     * @param \CMW\Entity\Roles\RoleEntity[] $userRoles
     * @param string $userCreated
     * @param string $userUpdated
     */
    public function __construct(int $userId, string $userMail, string $userUsername, string $userFirstName, string $userLastName, int $userState, string $userLastConnection, array $userRoles, string $userCreated, string $userUpdated)
    {
        $this->userId = $userId;
        $this->userMail = $userMail;
        $this->userUsername = $userUsername;
        $this->userFirstName = $userFirstName;
        $this->userLastName = $userLastName;
        $this->userState = $userState;
        $this->userLastConnection = $userLastConnection;
        $this->userRoles = $userRoles;
        $this->userCreated = $userCreated;
        $this->userUpdated = $userUpdated;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getMail(): string
    {
        return $this->userMail;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->userUsername;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->userFirstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->userLastName;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->userState;
    }

    /**
     * @return string
     */
    public function getLastConnection(): string
    {
        return $this->userLastConnection;
    }

    /**
     * @return \CMW\Entity\Roles\RoleEntity[]
     */
    public function getRoles(): array
    {
        return $this->userRoles;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->userCreated;
    }

    /**
     * @return string
     */
    public function getUpdated(): string
    {
        return $this->userUpdated;
    }

}