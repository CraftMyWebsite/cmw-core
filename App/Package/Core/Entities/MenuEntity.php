<?php

namespace CMW\Entity\Core;

use CMW\Controller\Users\UsersController;
use CMW\Entity\Users\RoleEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\MenusModel;
use CMW\Model\Users\RolesModel;
use CMW\Model\Users\UsersModel;

class MenuEntity
{
    private int $id;
    private string $name;
    private string $url;
    private int $isRestricted;
    private int $order;
    private int $targetBlank;
    /* @var RoleEntity[]|null $restrictedRoles */
    private ?array $restrictedRoles;

    /**
     * @param int $id
     * @param string $name
     * @param string $url
     * @param int $isRestricted
     * @param int $order
     * @param int $targetBlank
     * @param RoleEntity[]|null $restrictedRoles
     */
    public function __construct(int $id, string $name, string $url, int $isRestricted, int $order, int $targetBlank, ?array $restrictedRoles)
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->isRestricted = $isRestricted;
        $this->order = $order;
        $this->targetBlank = $targetBlank;
        $this->restrictedRoles = $restrictedRoles;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        if ($this->url === "") {
            return "#";
        }

        if(str_starts_with($this->url, 'http')){
            return $this->url;
        }

        return EnvManager::getInstance()->getValue("PATH_SUBFOLDER").$this->url;
    }

    /**
     * @return bool
     */
    public function isRestricted(): bool
    {
        return $this->isRestricted;
    }

    /**
     * @return bool
     */
    public function isUserAllowed(): bool
    {
        if (!$this->isRestricted){
            return true;
        }

        if (!UsersController::isUserLogged() && $this->isRestricted()){
            return false;
        }

        if ($this->restrictedRoles === null){
            return true;
        }

        foreach ($this->restrictedRoles as $restrictedRole){
            if (RolesModel::playerHasRole(UsersModel::getCurrentUser()?->getId(), $restrictedRole?->getId())){
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @return int
     */
    public function getLastMenuOrder(): int
    {
        return MenusModel::getInstance()->getLastMenuOrder();
    }

    /**
     * @return int
     */
    public function getLastSubMenuOrder(int $id): int
    {
        return MenusModel::getInstance()->getLastSubMenuOrder($id);
    }

    /**
     * @return bool
     */
    public function isTargetBlank(): bool
    {
        return $this->targetBlank;
    }

    /**
     * @return RoleEntity[]|null
     */
    public function getRestrictedRoles(): ?array
    {
        return $this->restrictedRoles;
    }
}