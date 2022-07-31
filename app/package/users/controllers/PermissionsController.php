<?php

namespace CMW\Controller\Users;

use CMW\Controller\Core\CoreController;
use CMW\Model\Users\PermissionsModel;

/**
 * Class: @permissionsController
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class PermissionsController extends CoreController
{

    /**
     * @return \CMW\Entity\Users\PermissionEntity[]
     */
    public function getParents(): array
    {
        return (new PermissionsModel())->getParents();
    }


}