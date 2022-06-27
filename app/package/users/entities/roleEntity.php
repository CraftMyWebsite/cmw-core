<?php

namespace CMW\Entity\Roles;

class roleEntity
{

    private int $roleId;
    private string $roleName;
    private string $roleDescription;
    private int $roleWeight;

    /**
     * @param int $roleId
     * @param string $roleName
     * @param string $roleDescription
     * @param int $roleWeight
     */
    public function __construct(int $roleId, string $roleName, string $roleDescription, int $roleWeight)
    {
        $this->roleId = $roleId;
        $this->roleName = $roleName;
        $this->roleDescription = $roleDescription;
        $this->roleWeight = $roleWeight;
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

}