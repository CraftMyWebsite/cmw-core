<?php

namespace CMW\Entity\Core;

use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Users\RoleEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Package\AbstractEntity;
use CMW\Model\Core\MenusModel;
use CMW\Model\Users\RolesModel;

class MenuEntity extends AbstractEntity
{
    private int $id;
    private string $name;
    private string $url;
    private int $isRestricted;
    private int $isCustomUrl;
    private int $order;
    private int $targetBlank;

    /* @var RoleEntity[]|null $restrictedRoles */
    private ?array $restrictedRoles;

    /**
     * @param int $id
     * @param string $name
     * @param string $url
     * @param int $isRestricted
     * @param int $isCustomUrl
     * @param int $order
     * @param int $targetBlank
     * @param RoleEntity[]|null $restrictedRoles
     */
    public function __construct(int $id, string $name, string $url, int $isRestricted, int $isCustomUrl, int $order, int $targetBlank, ?array $restrictedRoles)
    {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->isRestricted = $isRestricted;
        $this->isCustomUrl = $isCustomUrl;
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
        if ($this->url === '') {
            return '#';
        }

        if (str_starts_with($this->url, 'http')) {
            return $this->url;
        }

        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . $this->url;
    }

    /**
     * @return string
     */
    public function getUnformatedUrl(): string
    {
        return $this->url;
    }

    /**
     * @return bool
     * @desc allows you to check if you are on the active page to activate or not a class as "active" in your menu
     */
    public function urlIsActive(): bool
    {
        if (str_contains($_SERVER['REQUEST_URI'], $this->getUrl())) {
            return true;
        }
        if ($_SERVER['REQUEST_URI'] === '/' && $this->getUrl() === '/home') {
            return true;
        }
        return false;
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
    public function isCustomUrl(): bool
    {
        return $this->isCustomUrl;
    }

    /**
     * @return bool
     */
    public function isUserAllowed(): bool
    {
        if (!$this->isRestricted) {
            return true;
        }

        $currentUser = UsersSessionsController::getInstance()->getCurrentUser();

        if (is_null($currentUser) && $this->isRestricted()) {
            return false;
        }

        if ($this->restrictedRoles === null) {
            return true;
        }

        //TODO Bulk check
        foreach ($this->restrictedRoles as $restrictedRole) {
            if (RolesModel::playerHasRole($currentUser->getId(), $restrictedRole->getId())) {
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
     * @param int $id
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
