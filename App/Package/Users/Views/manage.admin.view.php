<?php

use CMW\Entity\Users\RoleEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/** @var UserEntity $user */
/** @var RoleEntity[] $roles */
/** @var UserEntity[] $userList */
$title = LangManager::translate('users.manage.title');
$description = LangManager::translate('users.manage.desc');
?>

<h3><i class="fa-solid fa-sliders"></i> <?= LangManager::translate('users.manage.title') ?></h3>

<div class="card">
    <div class="flex justify-between">
        <h6><?= LangManager::translate('users.manage.card_title_add') ?></h6>
        <button form="adduser" type="submit" class="btn-primary"><?= LangManager::translate('core.btn.add') ?></button>
    </div>
    <form id="adduser" method="post" action="add" class="grid-4">
        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
        <div>
            <label for="email"><?= LangManager::translate('users.users.mail') ?> :</label>
            <div class="input-group">
                <i class="fa-solid fa-at"></i>
                <input type="email" id="email" name="email" autocomplete="off"
                       placeholder="<?= LangManager::translate('users.users.mail') ?>" required>
            </div>
        </div>
        <div>
            <label for="pseudo"><?= LangManager::translate('users.users.pseudo') ?> :</label>
            <div class="input-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" id="pseudo" name="pseudo" autocomplete="off"
                       placeholder="<?= LangManager::translate('users.users.pseudo') ?>" required>
            </div>
        </div>
        <div>
            <label for="password"><?= LangManager::translate('users.users.password') ?> :</label>
            <div class="input-btn">
                <input type="password" id="password" name="password" placeholder="••••••" required/>
                <button onclick="generatePassword('password')" type="button"><i class="fa-solid fa-rotate"></i></button>
            </div>
        </div>
        <div>
            <label for="roles"><?= LangManager::translate('users.users.roles') ?> :</label>
            <select id="roles" class="choices choices__list--multiple" name="roles[]" multiple required>
                <?php foreach ($roles as $role): ?>
                    <option <?= $role->isDefault() ? 'selected' : '' ?> value="<?= $role->getId() ?>"><?= $role->getName() ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<div class="card mt-4">
    <h6><?= LangManager::translate('users.manage.card_title_list') ?></h6>
    <div class="table-container table-container-striped">
        <table id="table1" data-load-per-page="20">
            <thead>
            <tr>
                <th><?= LangManager::translate('users.users.mail') ?></th>
                <th><?= LangManager::translate('users.users.pseudo') ?></th>
                <th><?= LangManager::translate('users.users.role') ?></th>
                <th><?= LangManager::translate('users.users.creation') ?></th>
                <th><?= LangManager::translate('users.users.last_connection') ?></th>
                <th><?= LangManager::translate('users.users.login_methode') ?></th>
                <th class="text-center">2fa</th>
                <th class="text-center"><?= LangManager::translate('core.btn.edit') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($userList as $user): ?>
                <tr class="<?= !$user->getState() ? 'line-through' : '' ?>">
                    <td><?= $user->getMail() ?></td>
                    <td><?= $user->getPseudo() ?></td>
                    <td><?= $user->getHighestRole()?->getName() ?></td>
                    <td><?= $user->getCreated() ?></td>
                    <td><?= $user->getLastConnection() ?></td>
                    <td><?= ucfirst($user->getLoginMethode()) ?></td>
                    <td class="text-center"><?php if ($user->get2Fa()->isEnabled()): ?> <i
                            class="text-success fa-solid fa-check fa-lg"></i> <?php else: ?> <i
                            class="text-danger fa-solid fa-xmark fa-lg"></i> <?php endif; ?></td>
                    <td class="text-center space-x-2">
                        <a class=""
                           href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/users/manage/edit/<?= $user->getId() ?>">
                            <i class="text-info fa-solid fa-gears"></i>
                        </a>
                        <button data-modal-toggle="modal-danger-<?= $user->getId() ?>" type="button"><i
                                class="text-danger fas fa-trash-alt"></i></button>
                        <!--MODAL DANGER-->
                        <div id="modal-danger-<?= $user->getId() ?>" class="modal-container">
                            <div class="modal">
                                <div class="modal-header-danger">
                                    <h6><?= LangManager::translate('users.modal.delete') ?> <?= $user->getPseudo() ?>
                                        ?</h6>
                                    <button type="button" data-modal-hide="modal-danger-<?= $user->getId() ?>"><i
                                            class="fa-solid fa-xmark"></i></button>
                                </div>
                                <div class="modal-body">
                                    <?= LangManager::translate('users.modal.delete_message') ?>
                                </div>
                                <div class="modal-footer">
                                    <a href="<?= EnvManager::getInstance()->getValue('PATH_URL') ?>cmw-admin/users/delete/<?= $user->getId() ?>"
                                       type="button" class="btn-danger">
                                        <?= LangManager::translate('core.btn.delete') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>