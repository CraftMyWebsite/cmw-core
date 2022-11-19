<?php

use CMW\Controller\Users\PermissionsController;
use CMW\Entity\Users\RoleEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\PermissionsModel;
use CMW\Model\Users\RolesModel;
use CMW\Utils\SecurityService;

/**@var PermissionsController $permissionController */
/**@var PermissionsModel $permissionModel */
/** @var RoleEntity $role */
/** @var RolesModel $roleModel */
/* @var \CMW\Entity\Users\RoleEntity $role */

$title = LangManager::translate("users.roles.manage.title");
$description = LangManager::translate("users.roles.manage.desc"); ?>


<div >
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
         role="document">
        <div class="modal-content">
            <form method="post" action="">
                <?php (new SecurityService())->insertHiddenToken() ?>
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="roleEditModalTitle"><?= LangManager::translate('users.roles.manage.edit_title') ?>
                        <?= $role->getName() ?>
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i
                                data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block"><?= LangManager::translate("core.btn.close") ?></span>
                    </button>
                    <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block"><?= LangManager::translate("core.btn.edit") ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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
