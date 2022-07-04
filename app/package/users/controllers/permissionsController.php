<?php

namespace CMW\Controller\Permissions;

use CMW\Controller\coreController;
use CMW\Controller\Users\usersController;
use CMW\Model\Permissions\permissionsModel;
use CMW\Model\Roles\rolesModel;
use CMW\Model\Users\usersModel;
use JetBrains\PhpStorm\NoReturn;
use JsonException;

/**
 * Class: @permissionsController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class permissionsController extends coreController
{

    private usersModel $userModel;
    private rolesModel $roleModel;
    private permissionsModel $permissionsModel;

    public function __construct($theme_path = null)
    {
        parent::__construct($theme_path);
        $this->userModel = new usersModel();
        $this->roleModel = new rolesModel();
        $this->permissionsModel = new permissionsModel();
    }

}