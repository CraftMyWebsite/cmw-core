<?php

namespace CMW\Entity\Users;

use CMW\Manager\Package\AbstractEntity;

class UserEnforced2FaEntity extends AbstractEntity
{
    private RoleEntity $roleId;

    /**
     * @param RoleEntity $roleId
     */
    public function __construct(RoleEntity $roleId)
    {
        $this->roleId = $roleId;
    }

    public function getRole(): RoleEntity
    {
        return $this->roleId;
    }
}
