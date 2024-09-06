<?php

namespace CMW\Entity\Users;

use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Twofa\TwoFaManager;

class UserEnforced2FaEntity
{
    private RoleEntity $roleId;

    /**
     * @param \CMW\Entity\Users\RoleEntity $roleId
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
