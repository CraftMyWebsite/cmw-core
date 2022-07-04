<?php

namespace CMW\Entity\Roles;

class RoleEntity
{

    private int $roleId;
    private string $roleName;
    private string $roleDescription;
    private int $roleWeight;

    private ?array $rolePermissions;


    /**
     * @param int $roleId
     * @param string $roleName
     * @param string $roleDescription
     * @param int $roleWeight
     * @param array|null $rolePermissions
     */
    public function __construct(int $roleId, string $roleName, string $roleDescription, int $roleWeight, ?array $rolePermissions)
    {
        $this->roleId = $roleId;
        $this->roleName = $roleName;
        $this->roleDescription = $roleDescription;
        $this->roleWeight = $roleWeight;
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
     * @return array|null
     */
    public function getRolePermissions(): ?array
    {
        return $this->rolePermissions;
    }


}