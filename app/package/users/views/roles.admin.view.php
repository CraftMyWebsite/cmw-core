<?php

use CMW\Controller\Users\PermissionsController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\PermissionsModel;
use CMW\Model\Users\RolesModel;
use CMW\Utils\SecurityService;

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
                    <tr>
                        <?php foreach ($rolesList

                        as $role) : ?>
                    <tr>
                        <td><?= $role->getName() ?></td>
                        <td><?= $role->getDescription() ?></td>
                        <td class="text-center">
                            <button class="btn-clear" onclick="fillEditModal(<?= $role->getId() ?>)">
                                <i data-bs-toggle="modal" data-bs-target="#roleEditModal"
                                   class="text-primary fa-solid fa-gears"></i>
                            </button>

                            <button class="btn-clear">
                                <i data-bs-toggle="modal" onclick="deleteRole(<?= $role->getId() ?>)"
                                   data-bs-target="#roleDeleteModal"
                                   class="ms-2 text-danger fa-solid fa-trash"></i>
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


<!-- MODAL EDIT ROLE -->
<script>
    const modalEditData = (data) => {
        return `<div class="modal fade modal-xl" id="roleEditModal" tabindex="-1" role="dialog"
             aria-labelledby="roleEditModalTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
                 role="document">
                <div class="modal-content">
                <form method="post" action="edit">
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="roleEditModalTitle"><?= LangManager::translate('users.roles.manage.edit_title') ?> ${data.name}</h5>
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
                                        value="${data.name}"
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
                                        <input type="number" class="form-control" value="${data.weight}" placeholder="1" required>
                                            <div class="form-control-icon">
                                                <i class="fa-solid fa-weight-hanging"></i>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <h6><?= LangManager::translate("users.users.role_description") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control"
                                value="${data.description}"
                                 placeholder="<?= LangManager::translate("users.users.role_description") ?>" required>
                                    <div class="form-control-icon">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </div>
                            </div>
                            <h6><?= LangManager::translate("users.roles.manage.permissions_list") ?> :</h6>
                            <div class="row mx-auto">
                                    <?php showPermission($permissionModel, $permissionController->getParents(),
                                    $rolesModel) ?>
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
    `
    }
</script>

<!--MODAL ADD ROLE -->
<div class="modal fade modal-xl" id="roleAddModal" tabindex="-1" role="dialog" aria-labelledby="roleAddModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <form method="post" action="add">
                <?php (new SecurityService())->insertHiddenToken() ?>
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="roleAddModalTitle"><?= LangManager::translate("users.roles.manage.add") ?> </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i
                                data-feather="x"></i>
                    </button>
                </div>
                <div class="modal-body">
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
                    <h6><?= LangManager::translate("users.roles.manage.permissions_list") ?> :</h6>
                    <div class="row mx-auto">

                        <?php showPermission($permissionModel, $permissionController->getParents()) ?>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x d-block d-sm-none"></i>
                        <span class="d-none d-sm-block"><?= LangManager::translate("core.btn.close") ?></span>
                    </button>
                    <button type="submit" class="btn btn-primary ml-1" data-bs-dismiss="modal">
                        <i class="bx bx-check d-block d-sm-none"></i>
                        <span class="d-none d-sm-block"><?= LangManager::translate("core.btn.add") ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DELETE ROLE -->
<script>
    const modalDeleteRole = (roleId, roleName) => {
        return `<div class="modal fade" id="roleDeleteModal" tabindex="-1" role="dialog" aria-labelledby="roleDeleteModalTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
                 role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="roleDeleteModalTitle"><?= LangManager::translate('users.roles.manage.delete.title') ?></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i
                            data-feather="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>
                            <?= LangManager::translate('users.roles.manage.delete.content') ?>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                            <i class="bx bx-x d-block d-sm-none"></i>
                            <span class="d-none d-sm-block"><?= LangManager::translate('core.btn.close') ?></span>
                        </button>
                        <a href="delete/${roleId}" class="btn btn-danger ml-1">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block"><?= LangManager::translate('core.btn.confirm') ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>`
    }
</script>