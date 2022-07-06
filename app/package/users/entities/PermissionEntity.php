<?php

namespace CMW\Entity\Permissions;

class PermissionEntity
{

    private int $permissionId;
    private ?PermissionEntity $permissionParent;
    private string $permissionCode;
    private int $permissionEditable;

    /**
     * @param int $permissionId
     * @param PermissionEntity|null $permissionParent
     * @param string $permissionCode
     * @param int $permissionEditable
     */
    public function __construct(int $permissionId, ?PermissionEntity $permissionParent, string $permissionCode, int $permissionEditable)
    {
        $this->permissionId = $permissionId;
        $this->permissionParent = $permissionParent;
        $this->permissionCode = $permissionCode;
        $this->permissionEditable = $permissionEditable;
    }

    /**
     * @return int
     */
    public function getPermissionId(): int
    {
        return $this->permissionId;
    }

    /**
     * @return PermissionEntity|null
     */
    public function getPermissionParent(): ?PermissionEntity
    {
        return $this->permissionParent;
    }

    /**
     * @return string
     */
    public function getPermissionCode(): string
    {
        return $this->permissionCode;
    }

    /**
     * @return int
     */
    public function getPermissionEditable(): int
    {
        return $this->permissionEditable;
    }


}
