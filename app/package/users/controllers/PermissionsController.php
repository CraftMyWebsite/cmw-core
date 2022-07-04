<?php

namespace CMW\Controller\Permissions;

use CMW\Controller\CoreController;
use CMW\Controller\Users\UsersController;
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

    private UsersModel $userModel;
    private RolesModel $roleModel;
    private PermissionsModel $permissionsModel;

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->userModel = new UsersModel();
        $this->roleModel = new RolesModel();
        $this->permissionsModel = new PermissionsModel();
    }

}