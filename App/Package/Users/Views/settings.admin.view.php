<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\Users\UserSettingsEntity $settings */
/* @var RoleEntity[] $roles */
/* @var \CMW\Entity\Users\BlacklistedPseudoEntity[] $pseudos */

$title = LangManager::translate("users.settings.title");
$description = LangManager::translate("users.settings.desc"); ?>

<div class="page-title">
    <h3><i class="fa-solid fa-gears"></i> <?= LangManager::translate("users.settings.title") ?></h3>
    <button form="setting" type="submit" class="btn btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
</div>

<div class="grid-2">
    <div class="space-y-4">
        <div class="card">
            <div class="card-title">
                <h6><?= LangManager::translate("users.blacklist.title") ?></h6>
                <button type="button" class="btn-danger btn-mass-delete loading-btn" data-loading-btn="Chargement"
                        data-target-table="1"><?= LangManager::translate("core.btn.mass_delete") ?>
                </button>
            </div>
            <div class="table-container">
                <table class="table-checkeable" data-form-action="settings/blacklist/pseudo/deleteSelected" id="table1">
                    <thead>
                    <tr>
                        <th class="mass-selector"></th>
                        <th><?= LangManager::translate("users.blacklist.table.pseudo") ?></th>
                        <th><?= LangManager::translate("users.blacklist.table.date") ?></th>
                        <th class="text-center"><?= LangManager::translate("users.blacklist.table.action") ?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pseudos as $pseudo) : ?>
                        <tr>
                            <td class="item-selector" data-value="<?= $pseudo->getId() ?>"></td>
                            <td><?= $pseudo->getPseudo() ?></td>
                            <td><?= $pseudo->getDateBlacklistedFormatted() ?></td>
                            <td class="text-center space-x-2">
                                <button data-modal-toggle="modal-edit-<?= $pseudo->getId() ?>" class="text-info" type="button"><i class="fa-solid fa-gears"></i></button>
                                <button data-modal-toggle="modal-<?= $pseudo->getId() ?>" class="text-danger" type="button"><i class="fa-solid fa-trash"></i></button>
                            </td>

                            <!--MODAL DELETE-->
                            <div id="modal-<?= $pseudo->getId() ?>" class="modal-container">
                                <div class="modal">
                                    <div class="modal-header-danger">
                                        <h6><?= LangManager::translate("users.blacklist.delete.title") ?><?= $pseudo->getPseudo() ?></h6>
                                        <button type="button" data-modal-hide="modal-<?= $pseudo->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <div class="modal-body">
                                        <?= LangManager::translate("users.blacklist.delete.content") ?>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="settings/blacklist/pseudo/delete/<?= $pseudo->getId() ?>" type="button" class="btn-danger"><?= LangManager::translate("core.btn.delete") ?></a>
                                    </div>
                                </div>
                            </div>
                            <!--MODAL - EDIT-->
                            <div id="modal-edit-<?= $pseudo->getId() ?>" class="modal-container">
                                <div class="modal">
                                    <div class="modal-header">
                                        <h6><?= LangManager::translate("users.blacklist.edit.title") ?><?= $pseudo->getPseudo() ?></h6>
                                        <button type="button" data-modal-hide="modal-edit-<?= $pseudo->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                    </div>
                                    <form action="settings/blacklist/pseudo/edit/<?= $pseudo->getId() ?>" method="post">
                                        <?php (new SecurityManager())->insertHiddenToken() ?>
                                    <div class="modal-body">
                                        <div class="input-group">
                                            <i class="fas fa-user"></i>
                                            <input type="text" id="pseudo" name="pseudo" value="<?= $pseudo->getPseudo() ?>" placeholder="BadUserName" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn-primary"><?= LangManager::translate("core.btn.edit") ?></button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
                <h6><?= LangManager::translate('users.settings.blacklisted.pseudo.title') ?></h6>
                <form method="post" action="settings/blacklist/pseudo">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="pseudo" name="pseudo" placeholder="BadUserName" required>
                    </div>
                        <button type="submit" class="btn-primary btn-center">
                            <?= LangManager::translate('users.settings.blacklisted.pseudo.btn') ?>
                        </button>
                </form>
        </div>

        <div class="card mb-4">
            <h6>Double facteur obligatoire</h6>
            <fieldset class="form-group" disabled>
                <select class="form-select" id="forceTo" name="forceTo" required>
                    <option value="0">Coming Soon</option>
                    <option value="0">Personne</option>
                    <option value="1">Tout le monde</option>
                    <option value="2">Administrateurs</option>
                    <option value="3">Ayant le rôle :</option>
                </select>
            </fieldset>
            <div class="mt-2" id="listAllowedGroups">
                <h6><?= LangManager::translate("core.menus.add.group_select") ?> :</h6>
                <div class="form-group">
                    <select class="choices form-select" name="allowedGroups[]" multiple>
                        <?php foreach ($roles as $role): ?>
                            <option
                                value="<?= $role->getId() ?>"><?= $role->getName() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <form action="" id="setting" method="post" enctype="multipart/form-data">
        <?php (new SecurityManager())->insertHiddenToken() ?>

        <div class="card mb-4">
            <h6><?= LangManager::translate("users.settings.default_picture") ?></h6>
            <div class="grid-2">
                <img class="rounded-lg"
                     src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Uploads/Users/Default/<?= $settings->getDefaultImage() ?>"
                     alt="<?= LangManager::translate("users.settings.default_picture") ?>">
                <div class="drop-img-area" data-input-name="defaultPicture"></div>
            </div>
        </div>
        <div class="card mb-4">
            <h6><?= LangManager::translate('users.settings.resetPasswordMethod.label') ?></h6>
            <select id="basicSelect" name="reset_password_method" required>
                <option value="0" <?= $settings->getResetPasswordMethod() === 0 ? 'selected' : '' ?>>
                    <?= LangManager::translate("users.settings.resetPasswordMethod.options.0") ?>
                </option>
                <!--<option value="1" <?= $settings->getResetPasswordMethod() === 1 ? 'selected' : '' ?>>
                    <?= LangManager::translate("users.settings.resetPasswordMethod.options.1") ?>
                </option>-->
            </select>
        </div>

        <div class="card">
            <h6><?= LangManager::translate('users.settings.profile_view.label') ?></h6>
            <select class="form-select" id="basicSelect" name="profile_page" required>
                <option value="0" <?= $settings->getProfilePageStatus() === 0 ? 'selected' : '' ?>>
                    <?= LangManager::translate("users.settings.profile_view.options.0") ?>
                </option>

                <option value="1" <?= $settings->getProfilePageStatus() === 1 ? 'selected' : '' ?>>
                    <?= LangManager::translate("users.settings.profile_view.options.1") ?>
                </option>

                <option value="2" <?= $settings->getProfilePageStatus() === 2 ? 'selected' : '' ?>>
                    <?= LangManager::translate("users.settings.profile_view.options.2") ?>
                </option>
            </select>
        </div>
    </form>
</div>


<script>
    var basicSelect = document.getElementById('forceTo');
    var listAllowedGroups = document.getElementById('listAllowedGroups');

    basicSelect.addEventListener('change', function () {
        if (basicSelect.value === "3") {
            listAllowedGroups.style.display = 'block';
        } else {
            listAllowedGroups.style.display = 'none';
        }
    });

    if (basicSelect.value === "3") {
        listAllowedGroups.style.display = 'block';
    } else {
        listAllowedGroups.style.display = 'none';
    }
</script>





