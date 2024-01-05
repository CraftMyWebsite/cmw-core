<?php

use CMW\Controller\Users\PermissionsController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Users\PermissionsModel;
use CMW\Model\Users\RolesModel;

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
                    <tr class="">
                        <th class="text-center"><?= LangManager::translate("users.roles.manage.name") ?></th>
                        <th class="text-center"><?= LangManager::translate("users.roles.manage.description") ?></th>
                        <th class="text-center"><?= LangManager::translate("users.roles.manage.default.title") ?></th>
                        <th class="text-center"><?= LangManager::translate("core.btn.action") ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rolesList as $role) : ?>
                        <tr>
                            <td><?= $role->getName() ?></td>
                            <td><?= $role->getDescription() ?></td>
                            <td class="text-center">
                                <?php if ($role->isDefault()): ?>
                                    <i class="text-success fa-regular fa-circle-dot fa-beat-fade"></i>
                                <?php else: ?>
                                    <a href="set_default/<?= $role->getId() ?>"><i
                                            class="fa-regular fa-circle fa-2xs"><span hidden>a</span></i></a>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a class="me-2" href="manage/edit/<?= $role->getId() ?>">
                                    <i class="text-primary fa-solid fa-gears"></i>
                                </a>

                                <a type="button" data-bs-toggle="modal" data-bs-target="#delete-<?= $role->getId() ?>">
                                    <i class="text-danger fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- MODAL DELETE ROLE -->
                        <div class="modal fade text-left" id="delete-<?= $role->getId() ?>" tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                 role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title white" id="myModalLabel160">
                                            Supprimer <?= $role->getName() ?> ?</h5>
                                    </div>
                                    <div class="modal-body text-left">
                                        La suppression de ce rôle est définitive !<br>
                                        Aucun retour possible !
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light-secondary"
                                                data-bs-dismiss="modal">
                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                        </button>
                                        <a href="/cmw-admin/roles/delete/<?= $role->getId() ?>"
                                           class="btn btn-danger">
                                            <span class=""><?= LangManager::translate("core.btn.delete") ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="d-flex flex-wrap justify-content-between">
                    <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'cmw-admin/roles/permissions/import' ?>" class="btn btn-primary">Importer permissions</a>

                    <button data-bs-toggle="modal" data-bs-target="#roleAddModal" type="button"
                            class="btn btn-primary">
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
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered" role="document">
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
                                        <input type="number" name="weight" class="form-control"
                                               onkeyup="checkIfWeightsIsAlreadyTaken(this.value)"
                                               placeholder="1" required>
                                        <div class="form-control-icon">
                                            <i class="fa-solid fa-weight-hanging"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-9">
                                    <h6><?= LangManager::translate("users.users.role_description") ?> :</h6>
                                    <div class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="description"
                                               placeholder="<?= LangManager::translate("users.users.role_description") ?>"
                                               required>
                                        <div class="form-control-icon">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3">
                                    <div class="form-switch">
                                        <input class="me-1 form-check-input permission-input" type="checkbox" value="1"
                                               id="isDefault" name="isDefault">

                                        <label class="form-check-label" for="isDefault">
                                            <?= LangManager::translate("users.roles.manage.default.title") ?> :
                                            <i data-bs-toggle="tooltip"
                                               title="<?= LangManager::translate('users.roles.manage.default.tips') ?>"
                                               class="fa-sharp fa-solid fa-circle-question"></i>
                                        </label>
                                    </div>
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
                        <button type="submit" class="btn btn-primary ml-1">
                            <?= LangManager::translate("core.btn.add") ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Trigger perm * and disabled all others perms checkbox -->
<script>
    const inputs = document.getElementsByClassName("permission-input")

    const checkChild = (parentElement) => {
        parentElement.parentElement.parentElement.children.item(1).classList.toggle("d-none")
    }

    for (const inp of inputs) {

        inp.onchange = () => checkChild(inp);

    }

</script>