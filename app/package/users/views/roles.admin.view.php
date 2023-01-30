<?php

use CMW\Controller\Users\PermissionsController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\PermissionsModel;
use CMW\Model\Users\RolesModel;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\Users\RoleEntity[] $rolesList */
/**@var PermissionsController $permissionController */
/**@var PermissionsModel $permissionModel */
/**@var RolesModel $rolesModel */

$title = LangManager::translate("users.roles.manage.title");
$description = LangManager::translate("users.roles.manage.desc"); ?>

<section>
    <div class="col-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4><?= LangManager::translate("users.roles.manage.title") ?></h4>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class=""><?= LangManager::translate("users.roles.manage.name") ?></th>
                        <th class=""><?= LangManager::translate("users.roles.manage.description") ?></th>
                        <th class="text-center"><?= LangManager::translate("core.btn.action") ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rolesList as $role) : ?>
                        <tr>
                            <td><?= $role->getName() ?></td>
                            <td><?= $role->getDescription() ?></td>
                            <td class="text-center">
                                <a href="edit/<?= $role->getId() ?>">
                                    <i class="text-primary fa-solid fa-gears"></i>
                                </a>

                                <button class="btn-clear" onclick="deleteRole(<?= $role->getId() ?>)">
                                    <i class="ms-2 text-danger fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="text-end ">
                    <button data-bs-toggle="modal" data-bs-target="#roleAddModal" type="button" class="btn btn-primary">
                        <?= LangManager::translate("users.roles.manage.add") ?>
                    </button>
                </div>

            </div>
        </div>
    </div>
</section>


<!--MODAL ADD ROLE -->
<div class="modal fade modal-xl" id="roleAddModal" tabindex="-1" role="dialog" aria-labelledby="roleAddModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form method="post" action="add">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="roleAddModalTitle"><?= LangManager::translate("users.roles.manage.add") ?> </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i
                                data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
                <div class="card-in-card mt-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-6">
                            <h6><?= LangManager::translate("users.users.role") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" name="name" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.role") ?>" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-id-card-clip"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <h6><?= LangManager::translate("users.users.weight") ?> :
                                <i data-bs-toggle="tooltip"
                                   title="<?= LangManager::translate('users.roles.manage.weightTips') ?>"
                                   class="fa-sharp fa-solid fa-circle-question"></i>
                            </h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="number" name="weight" class="form-control" placeholder="1" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-weight-hanging"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                        <h6><?= LangManager::translate("users.users.role_description") ?> :</h6>
                        <div class="form-group position-relative has-icon-left">
                            <input type="text" class="form-control" name="description"
                                   placeholder="<?= LangManager::translate("users.users.role_description") ?>" required>
                            <div class="form-control-icon">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-in-card mt-4">
                    <div class="card-body">
                        <h6><?= LangManager::translate("users.roles.manage.permissions_list") ?> :</h6>
                                        <div class="row mx-auto">
                                        <?php showPermission($permissionModel, $permissionController->getParents()) ?>
                    </div>
                </div>     
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="button">
                        <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <?= LangManager::translate("core.btn.close") ?>
                        </button>
                        <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal">
                            <?= LangManager::translate("core.btn.add") ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DELETE ROLE -->
<script>
    const modalDeleteRole = roleId => {
        return '' +
                `<div class="modal fade" id="roleDeleteModal" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="roleDeleteModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="roleDeleteModalTitle">
                                    <?= LangManager::translate('users.roles.manage.delete.title') ?>
                                </h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <i data-feather="x"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    <?= LangManager::translate('users.roles.manage.delete.content') ?>
                                </p>
                            </div>
                            <div class="modal-footer">
                                <div class="buttons">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                                        <?= LangManager::translate('core.btn.close') ?>
                                    </button>
                                    <a href="delete/${roleId}" class="btn btn-danger ml-1">
                                        <?= LangManager::translate('core.btn.confirm') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`
    }
</script>

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