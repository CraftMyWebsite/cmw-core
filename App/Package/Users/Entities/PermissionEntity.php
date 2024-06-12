<?php

namespace CMW\Entity\Users;

use CMW\Manager\Lang\LangManager;

class PermissionEntity
{

    private int $permissionId;
    private ?PermissionEntity $permissionParent;
    private string $permissionCode;
    private ?string $permissionDescription;

    /**
     * @param int $permissionId
     * @param PermissionEntity|null $permissionParent
     * @param string $permissionCode
     * @param string|null $permissionDescription
     */
    public function __construct(int $permissionId, ?PermissionEntity $permissionParent, string $permissionCode, ?string $permissionDescription)
    {
        $this->permissionId = $permissionId;
        $this->permissionParent = $permissionParent;
        $this->permissionCode = $permissionCode;
        $this->permissionDescription = $permissionDescription;
    }


    public function __toString(): string
    {

        $parent = $this->getParent() ?? "<u>Aucun parent !</u>";

        return <<<HTML
        <div>
            <h4>Permission #{$this->getId()}</h4>
            <ul>
                <li><b>Parent: </b> $parent</li>
                <br>
                <li><b>Name (Code): </b> {$this->getCode()}</li>
            </ul>
        </div>
        HTML;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->permissionId;
    }

    /**
     * @return PermissionEntity|null
     */
    public function getParent(): ?PermissionEntity
    {
        return $this->permissionParent;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->permissionCode;
    }

    /**
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->permissionDescription;
    }


    public function hasParent(): bool
    {
        return !is_null($this->permissionParent);
    }


}
