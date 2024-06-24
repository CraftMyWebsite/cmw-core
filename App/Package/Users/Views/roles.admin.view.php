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

<div class="page-title">
    <h3><i class="fa-solid fa-gavel"></i> <?= LangManager::translate("users.roles.manage.title") ?></h3>
    <div>
        <?php if (EnvManager::getInstance()->getValue('DEVMODE')): ?>
            <button data-modal-toggle="modal-import" class="btn-danger" type="button">FLUSH permissions</button>
        <?php endif; ?>
        <button data-modal-toggle="modal-create" class="btn-primary" type="button"><?= LangManager::translate("users.roles.manage.add") ?></button>
    </div>
</div>

<div id="modal-import" class="modal-container">
    <div class="modal">
        <div class="modal-header">
            <h6>FLUSH permissions</h6>
            <button type="button" data-modal-hide="modal-import"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body">
            <div class="alert-danger">
                <p><i class="fa-solid fa-circle-info"></i> Ceci va réinitialiser tous vos rôles ! (sauf Administrateur)</p>
            </div>
            <p>Flusher les permissions est un outil de débogage souvent utilisé par les développeurs qui souhaitent
                forcer l'ajout manuel des permissions de leurs fichiers Permissions.php se trouvant dans le dossier
                Init.</p>
        </div>
        <div class="modal-footer">
            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'cmw-admin/roles/permissions/import' ?>"
               class="btn-danger">FLUSHER</a>
        </div>
    </div>
</div>

<div class="table-container table-container-striped">
    <table>
        <thead>
        <tr>
            <th><?= LangManager::translate("users.roles.manage.name") ?></th>
            <th>Poids
                <button data-tooltip-target="tooltip-top" data-tooltip-placement="top"><i
                        class="fa-solid fa-circle-info"></i></button>
                <div id="tooltip-top" role="tooltip" class="tooltip-content lowercase">
                    Plus le poids est élevé, plus le rôle est important.
                </div>
            </th>
            <th><?= LangManager::translate("users.roles.manage.description") ?></th>
            <th class="text-center"><?= LangManager::translate("users.roles.manage.default.title") ?></th>
            <th class="text-center"><?= LangManager::translate("core.btn.action") ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rolesList as $role) : ?>
            <tr>
                <td><b><?= $role->getName() ?></b></td>
                <td><?= $role->getWeight() ?></td>
                <td><?= $role->getDescription() ?></td>
                <td class="text-center">
                    <?php if ($role->isDefault()): ?>
                        <i class="text-success fa-regular fa-circle-dot fa-beat-fade"></i>
                    <?php elseif ($role->getId() !== 5): ?>
                        <a href="set_default/<?= $role->getId() ?>"><i
                                class="fa-regular fa-circle fa-2xs"><span hidden>a</span></i></a>
                    <?php endif; ?>
                </td>
                <td class="text-center space-x-2">
                    <a href="manage/edit/<?= $role->getId() ?>">
                        <i class="text-info fa-solid fa-gears"></i>
                    </a>
                    <?php if ($role->getId() !== 5): ?>
                    <button data-modal-toggle="modal-delete-<?= $role->getId() ?>" class="text-danger" type="button"><i
                            class="text-danger fas fa-trash-alt"></i></button>
                    <?php endif; ?>
                </td>
            </tr>
            <!-- MODAL DELETE ROLE -->
        <?php if ($role->getId() !== 5): ?>
            <div id="modal-delete-<?= $role->getId() ?>" class="modal-container">
                <div class="modal">
                    <div class="modal-header-danger">
                        <h6><?= LangManager::translate("users.roles.manage.delete.title") ?> <?= $role->getName() ?> ?</h6>
                        <button type="button" data-modal-hide="modal-delete-<?= $role->getId() ?>"><i
                                class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <?= LangManager::translate("users.roles.manage.delete.content") ?>
                    </div>
                    <div class="modal-footer">
                        <a href="/cmw-admin/roles/delete/<?= $role->getId() ?>"
                           class="btn btn-danger">
                            <span class=""><?= LangManager::translate("core.btn.delete") ?></span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="modal-create" class="modal-container">
    <div class="modal-xl">
        <div class="modal-header">
            <h6><?= LangManager::translate("users.roles.manage.add") ?></h6>
            <button type="button" data-modal-hide="modal-create"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="post" action="add">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div class="modal-body">
                <div class="grid-4">
                    <div>
                        <label for="name"><?= LangManager::translate("users.users.role") ?> :</label>
                        <div class="input-group">
                            <i class="fa-solid fa-id-card-clip"></i>
                            <input type="text" id="name" name="name"
                                   placeholder="<?= LangManager::translate("users.users.role") ?>" required>
                        </div>
                    </div>
                    <div>
                        <label for="weight"><?= LangManager::translate("users.users.weight") ?> :</label>
                        <div class="input-group">
                            <i class="fa-solid fa-weight-hanging"></i>
                            <input type="number" name="weight" id="weight"
                                   onkeyup="checkIfWeightsIsAlreadyTaken(this.value)"
                                   placeholder="1" required>
                        </div>
                    </div>
                    <div>
                        <label for="description"><?= LangManager::translate("users.users.role_description") ?> :</label>
                        <div class="input-group">
                            <i class="fa-solid fa-circle-info"></i>
                            <input type="text" id="description" name="description"
                                   placeholder="<?= LangManager::translate("users.users.role_description") ?>"
                                   required>
                        </div>
                    </div>
                    <div>
                        <div>
                            <label class="toggle">
                                <p class="toggle-label"><?= LangManager::translate("users.roles.manage.default.title") ?> :</p>
                                <input class="toggle-input" type="checkbox"
                                       id="isDefault" name="isDefault">
                                <div class="toggle-slider"></div>
                            </label>
                        </div>
                    </div>
                </div>
                <h6><?= LangManager::translate("users.roles.manage.permissions_list") ?> :</h6>
                    <div class="flex-col flex-wrap mx-auto space-y-4">
                        <?php showPermission($permissionModel, $permissionController->getParents()) ?>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-primary"><?= LangManager::translate("core.btn.add") ?></button>
            </div>
        </form>
    </div>
</div>