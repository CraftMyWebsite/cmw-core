<?php

namespace CMW\Entity\Users;

use CMW\Controller\Core\CoreController;
use CMW\Model\Users\UsersModel;

class UserEntity
{

    private int $userId;
    private string $userMail;
    private string $userPseudo;
    private string $userFirstName;
    private string $userLastName;
    private int $userState;
    private string $userKey;
    private ?string $user2FaSecret;
    private string $userLastConnection;
    /** @var \CMW\Entity\Users\RoleEntity|\CMW\Entity\Users\RoleEntity[] $userRoles */
    private array $userRoles;
    private ?RoleEntity $userHighestRole;
    private string $userCreated;
    private string $userUpdated;
    private ?UserPictureEntity $userPicture;


    /**
     * @param int $userId
     * @param string $userMail
     * @param string $userPseudo
     * @param string $userFirstName
     * @param string $userLastName
     * @param int $userState
     * @param string $userKey
     * @param string|null $user2FaSecret
     * @param string $userLastConnection
     * @param \CMW\Entity\Users\RoleEntity[] $userRoles
     * @param ?\CMW\Entity\Users\RoleEntity $userHighestRole
     * @param string $userCreated
     * @param string $userUpdated
     * @param \CMW\Entity\Users\UserPictureEntity|null $userPicture
     */
    public function __construct(int $userId, string $userMail, string $userPseudo, string $userFirstName, string $userLastName, int $userState, string $userKey, ?string $user2FaSecret, string $userLastConnection, array $userRoles, ?RoleEntity $userHighestRole, string $userCreated, string $userUpdated, ?UserPictureEntity $userPicture)
    {
        $this->userId = $userId;
        $this->userMail = $userMail;
        $this->userPseudo = $userPseudo;
        $this->userFirstName = $userFirstName;
        $this->userLastName = $userLastName;
        $this->userState = $userState;
        $this->userKey = $userKey;
        $this->user2FaSecret = $user2FaSecret;
        $this->userLastConnection = $userLastConnection;
        $this->userRoles = $userRoles;
        $this->userHighestRole = $userHighestRole;
        $this->userCreated = $userCreated;
        $this->userUpdated = $userUpdated;
        $this->userPicture = $userPicture;
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
    public function getPseudo(): string
    {
        return $this->userPseudo;
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
    public function getUserKey(): string
    {
        return $this->userKey;
    }

    /**
     * @return string|null
     */
    public function get2FaSecret(): ?string
    {
        return $this->user2FaSecret;
    }

    /**
     * @return bool
     */
    public function isUsing2Fa(): bool
    {
        return !is_null($this->user2FaSecret);
    }

    /**
     * @return string
     * @Desc date
     */
    public function getLastConnection(): string
    {
        return CoreController::formatDate($this->userLastConnection);
    }

    /**
     * @return \CMW\Entity\Users\RoleEntity[]
     */
    public function getRoles(): array
    {
        return $this->userRoles;
    }

    /**
     * @return ?\CMW\Entity\Users\RoleEntity
     */
    public function getHighestRole(): ?RoleEntity
    {
        return $this->userHighestRole;
    }

    /**
     * @return string
     * @desc date
     */
    public function getCreated(): string
    {
        return CoreController::formatDate($this->userCreated);
    }

    /**
     * @return string
     * @desc date
     */
    public function getUpdated(): string
    {
        return CoreController::formatDate($this->userUpdated);
    }

    /**
     * @return \CMW\Entity\Users\UserPictureEntity | null
     */
    public function getUserPicture(): ?UserPictureEntity
    {
        return $this->userPicture;
    }

    /**
     * @return bool
     * @desc Return true if the current user is the page owner
     */
    public function isViewerIsCurrentUser(): bool
    {
        return UsersModel::getCurrentUser()?->userId === $this->userId;
    }

}