<?php

use CMW\Controller\Users\PermissionsController;
use CMW\Entity\Users\RoleEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Users\PermissionsModel;
use CMW\Model\Users\RolesModel;

/* @var PermissionsController $permissionController */
/* @var PermissionsModel $permissionModel */
/** @var RoleEntity $role */
/** @var RolesModel $roleModel */
/* @var \CMW\Entity\Users\RoleEntity $role */

$title = LangManager::translate('users.roles.manage.title');
$description = LangManager::translate('users.roles.manage.desc');
?>
<div class="page-title">
    <h3><?= LangManager::translate('users.roles.manage.edit_title') ?>
        <?= $role->getName() ?></h3>
    <button form="editRole" type="submit" class="btn-primary"><?= LangManager::translate('core.btn.edit') ?></button>
</div>

<form id="editRole" method="post" action="">
    <?php (new SecurityManager())->insertHiddenToken() ?>
<div class="card">

        <div class="grid-3">
            <div>
                <label for="name"><?= LangManager::translate('users.users.role') ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-id-card-clip"></i>
                    <input type="text" id="name" name="name"
                           value="<?= $role->getName() ?>"
                           placeholder="<?= LangManager::translate('users.users.role') ?>" required>
                </div>
            </div>
            <div>
                <label for="weight"><?= LangManager::translate('users.users.weight') ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-weight-hanging"></i>
                    <input type="number" id="weight" name="weight" class="form-control"
                           value="<?= $role->getWeight() ?>"
                           placeholder="1"
                           required>
                </div>
            </div>
            <div>
                <label for="description"><?= LangManager::translate('users.users.role_description') ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-house"></i>
                    <input type="text" id="description" name="description"
                           placeholder="<?= LangManager::translate('users.users.role_description') ?>"
                           value="<?= $role->getDescription() ?>"
                           required>
                </div>
            </div>
        </div>


</div>

<div class="center-flex mt-4">
    <div class="card space-y-4 flex-content-lg">
        <h6><?= LangManager::translate('users.roles.manage.permissions_list') ?> :</h6>
        <?php showPermission($permissionModel, $permissionController->getParents(), $roleModel, $role); ?>
    </div>
</div>
</form>

