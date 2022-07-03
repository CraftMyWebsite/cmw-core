<?php

namespace CMW\Entity\Roles;

class roleEntity
{

    private int $roleId;
    private string $roleName;
    private string $roleDescription;
    private int $roleWeight;

    private int $rolePermissionId;
    private string $rolePermissionCode;
    private int $rolePermissionRoleId;

    /**
     * @param int $roleId
     * @param string $roleName
     * @param string $roleDescription
     * @param int $roleWeight
     * @param int $rolePermissionId
     * @param string $rolePermissionCode
     * @param int $rolePermissionRoleId
     */
    public function __construct(int $roleId, string $roleName, string $roleDescription, int $roleWeight, int $rolePermissionId, string $rolePermissionCode, int $rolePermissionRoleId)
    {
        $this->roleId = $roleId;
        $this->roleName = $roleName;
        $this->roleDescription = $roleDescription;
        $this->roleWeight = $roleWeight;

        $this->rolePermissionId = $rolePermissionId;
        $this->rolePermissionCode = $rolePermissionCode;
        $this->rolePermissionRoleId = $rolePermissionRoleId;
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
     * @return int
     */
    public function getRolePermissionId(): int
    {
        return $this->rolePermissionId;
    }

    /**
     * @return string
     */
    public function getRolePermissionCode(): string
    {
        return $this->rolePermissionCode;
    }

    /**
     * @return int
     */
    public function getRolePermissionRoleId(): int
    {
        return $this->rolePermissionRoleId;
    }


}