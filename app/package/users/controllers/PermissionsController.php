<?php

namespace CMW\Controller\Permissions;

use CMW\Controller\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Entity\Roles\RoleEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Model\Permissions\PermissionsModel;
use CMW\Model\Roles\RolesModel;
use CMW\Model\Users\UsersModel;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @permissionsController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PermissionsController extends CoreController
{

    /**
     * @return \CMW\Entity\Permissions\PermissionEntity[]
     */
    public function getParents(): array
    {
        return (new PermissionsModel())->getParents();
    }


}