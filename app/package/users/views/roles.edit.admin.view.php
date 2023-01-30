<?php

use CMW\Controller\Users\PermissionsController;
use CMW\Entity\Users\RoleEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\PermissionsModel;
use CMW\Model\Users\RolesModel;
use CMW\Manager\Security\SecurityManager;

/**@var PermissionsController $permissionController */
/**@var PermissionsModel $permissionModel */
/** @var RoleEntity $role */
/** @var RolesModel $roleModel */
/* @var \CMW\Entity\Users\RoleEntity $role */

$title = LangManager::translate("users.roles.manage.title");
$description = LangManager::translate("users.roles.manage.desc"); ?>
<section>
    <div class="card">
        <div class="card-header">
            <h4><?= LangManager::translate('users.roles.manage.edit_title') ?>
                        <?= $role->getName() ?></h4>
        </div>
        <div class="card-body">
            <form method="post" action="">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div class="row">
                <div class="col-12 col-lg-6">
                    <h6><?= LangManager::translate("users.users.role") ?> :</h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="text" class="form-control"
                               value="<?= $role->getName() ?>"
                               placeholder="<?= LangManager::translate("users.users.role") ?>" required>
                        <div class="form-control-icon">
                            <i class="fa-solid fa-id-card-clip"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <h6><?= LangManager::translate('users.users.weight') ?> :
                        <i data-bs-toggle="tooltip"
                           title="<?= LangManager::translate('users.roles.manage.weightTips') ?>"
                           class="fa-sharp fa-solid fa-circle-question"></i>
                    </h6>
                    <div class="form-group position-relative has-icon-left">
                        <input type="number" class="form-control" value="<?= $role->getWeight() ?>" placeholder="1"
                               required>
                        <div class="form-control-icon">
                            <i class="fa-solid fa-weight-hanging"></i>
                        </div>
                    </div>
                </div>
            </div>
            <h6><?= LangManager::translate("users.users.role_description") ?> :</h6>
            <div class="form-group position-relative has-icon-left">
                <input type="text" class="form-control"
                       value="<?= $role->getDescription() ?>"
                       placeholder="<?= LangManager::translate("users.users.role_description") ?>" required>
                <div class="form-control-icon">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
            </div>
            <h6><?= LangManager::translate("users.roles.manage.permissions_list") ?> :</h6>
            <div class="row mx-auto">
                <?php showPermission($permissionModel, $permissionController->getParents(), $roleModel, $role); ?>
            </div>


            <div class="text-end ">
                <button type="submit" class="btn btn-primary"><?= LangManager::translate("core.btn.edit") ?></button>
            </div>
            </form>
        </div>
    </div>
</section>

<!-- Trigger perm * and disabled all others perms checkbox -->
<script>
    const inputs = document.getElementsByClassName("permission-input")

    const checkChild = (parentElement) => {
        const group = parentElement.parentElement.parentElement.parentElement.parentElement
        const els = group.getElementsByClassName("permission-input")
        for (const item of els) {
            item.parentElement.parentElement.parentElement.classList.toggle("d-none")
        }
        parentElement.parentElement.parentElement.parentElement.classList.toggle("d-none")
    }

    for (const inp of inputs) {

        inp.onchange = () => checkChild(inp);

    }

</script>
