<?php

namespace CMW\Entity\Permissions;

class permissionEntity
{
    private string $permissionParentCode;
    private string $permissionParentPackage;
    private int $permissionParentEditable;

    private string $permissionChildCode;
    private string $permissionChildParent;
    private int $permissionChildEditable;

    private int $permissionDescId;
    private ?string $permissionDescCodeParent;
    private ?string $permissionDescCodeChild;
    private string $permissionDescValue;
    private string $permissionDescLang;


    /**
     * @param string $permissionParentCode
     * @param string $permissionParentPackage
     * @param int $permissionParentEditable
     * @param string $permissionChildCode
     * @param string $permissionChildParent
     * @param int $permissionChildEditable
     * @param int $permissionDescId
     * @param string|null $permissionDescCodeParent
     * @param string|null $permissionDescCodeChild
     * @param string $permissionDescValue
     * @param string $permissionDescLang
     */
    public function __construct(string $permissionParentCode, string $permissionParentPackage, int $permissionParentEditable, string $permissionChildCode, string $permissionChildParent, int $permissionChildEditable, int $permissionDescId, ?string $permissionDescCodeParent, ?string $permissionDescCodeChild, string $permissionDescValue, string $permissionDescLang)
    {
        $this->permissionParentCode = $permissionParentCode;
        $this->permissionParentPackage = $permissionParentPackage;
        $this->permissionParentEditable = $permissionParentEditable;
        $this->permissionChildCode = $permissionChildCode;
        $this->permissionChildParent = $permissionChildParent;
        $this->permissionChildEditable = $permissionChildEditable;
        $this->permissionDescId = $permissionDescId;
        $this->permissionDescCodeParent = $permissionDescCodeParent;
        $this->permissionDescCodeChild = $permissionDescCodeChild;
        $this->permissionDescValue = $permissionDescValue;
        $this->permissionDescLang = $permissionDescLang;
    }

    /**
     * @return string
     */
    public function getPermissionParentCode(): string
    {
        return $this->permissionParentCode;
    }

    /**
     * @return string
     */
    public function getPermissionParentPackage(): string
    {
        return $this->permissionParentPackage;
    }

    /**
     * @return int
     */
    public function getPermissionParentEditable(): int
    {
        return $this->permissionParentEditable;
    }

    /**
     * @return string
     */
    public function getPermissionChildCode(): string
    {
        return $this->permissionChildCode;
    }

    /**
     * @return string
     */
    public function getPermissionChildParent(): string
    {
        return $this->permissionChildParent;
    }

    /**
     * @return int
     */
    public function getPermissionChildEditable(): int
    {
        return $this->permissionChildEditable;
    }

    /**
     * @return int
     */
    public function getPermissionDescId(): int
    {
        return $this->permissionDescId;
    }

    /**
     * @return string|null
     */
    public function getPermissionDescCodeParent(): ?string
    {
        return $this->permissionDescCodeParent;
    }

    /**
     * @return string|null
     */
    public function getPermissionDescCodeChild(): ?string
    {
        return $this->permissionDescCodeChild;
    }

    /**
     * @return string
     */
    public function getPermissionDescValue(): string
    {
        return $this->permissionDescValue;
    }

    /**
     * @return string
     */
    public function getPermissionDescLang(): string
    {
        return $this->permissionDescLang;
    }




}
