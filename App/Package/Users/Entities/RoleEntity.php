<?php

namespace CMW\Entity\Users;

class RoleEntity
{

    private int $roleId;
    private string $roleName;
    private string $roleDescription;
    private int $roleWeight;
    private bool $roleIsDefault;

    private array $rolePermissions;


    /**
     * @param int $roleId
     * @param string $roleName
     * @param string $roleDescription
     * @param int $roleWeight
     * @param bool $roleIsDefault
     * @param PermissionEntity[] $rolePermissions
     */
    public function __construct(int $roleId, string $roleName, string $roleDescription, int $roleWeight, bool $roleIsDefault, array $rolePermissions)
    {
        $this->roleId = $roleId;
        $this->roleName = $roleName;
        $this->roleDescription = $roleDescription;
        $this->roleWeight = $roleWeight;
        $this->roleIsDefault = $roleIsDefault;
        $this->rolePermissions = $rolePermissions;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->roleId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->roleName;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->roleDescription;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->roleWeight;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->roleIsDefault;
    }


    /**
     * @return PermissionEntity[]
     */
    public function getPermissions(): array
    {
        return $this->rolePermissions;
    }


}