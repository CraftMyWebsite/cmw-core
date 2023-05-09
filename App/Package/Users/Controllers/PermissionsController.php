<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\CoreController;
use CMW\Manager\Package\AbstractController;
use CMW\Model\Users\PermissionsModel;

/**
 * Class: @permissionsController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PermissionsController extends AbstractController
{

    /**
     * @return \CMW\Entity\Users\PermissionEntity[]
     */
    public function getParents(): array
    {
        return PermissionsModel::getInstance()->getParents();
    }


}