<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Users\RolesModel;

$title = LangManager::translate('users.edit.title');
$description = LangManager::translate('users.edit.desc');

$scripts = '<script src="' . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Js/main.js"></script>';

/** @var \CMW\Entity\Users\UserEntity $user */
/** @var \CMW\Entity\Users\RoleEntity[] $roles */
?>

<div class="page-title">
    <h3><i class="fa-solid fa-gears"></i> Édition de : <?= $user->getPseudo() ?></h3>
    <div class="lg:flex space-x-2">
        <a href="../state/<?= $user->getId() ?>/<?= $user->getState() ?>" type="submit"
           class="btn btn-<?= ($user->getState()) ? 'danger' : 'success' ?>"><i
                class="fa fa-user-slash"></i> <?= ($user->getState()) ? LangManager::translate('users.edit.disable_account') : LangManager::translate('users.edit.activate_account') ?>
        </a>
        <?php if ($user->useNativeLoginMethode()): ?>
            <form method="post"
                  action="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login/forgot">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <input type="hidden" value="<?= $user->getMail() ?>" name="mail">
                <button type="submit" class="btn btn-warning">
                    <i class="fa fa-arrows-rotate"></i>
                    <?= LangManager::translate('users.edit.reset_password') ?>
                </button>
            </form>
        <?php endif; ?>
        <button type="submit" form="userprofile"
                class="btn btn-primary"><?= LangManager::translate('core.btn.save', lineBreak: true) ?></button>
    </div>
</div>

<div class="grid-3">
    <form action="" method="post" id="userprofile" class="col-span-2 grid-2">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <div class="card">
            <div>
                <label for="email"><?= LangManager::translate('users.users.mail') ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-envelope"></i>
                    <input type="email" id="email" name="email" value="<?= $user->getMail() ?>"
                           placeholder="<?= LangManager::translate('users.users.mail') ?>" required>
                </div>
            </div>
            <div>
                <label for="pseudo"><?= LangManager::translate('users.users.pseudo') ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-signature"></i>
                    <input type="text" id="pseudo" name="pseudo" value="<?= $user->getPseudo() ?>"
                           placeholder="<?= LangManager::translate('users.users.pseudo') ?>" required>
                </div>
            </div>
            <div>
                <label for="name"><?= LangManager::translate('users.users.firstname') ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-id-card"></i>
                    <input type="text" id="name" name="name" value="<?= $user->getFirstName() ?>"
                           placeholder="<?= LangManager::translate('users.users.firstname') ?>">
                </div>
            </div>
            <div>
                <label for="lastname"><?= LangManager::translate('users.users.surname') ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-id-card"></i>
                    <input type="text" id="lastname" name="lastname"
                           value="<?= $user->getLastName() ?>"
                           placeholder="<?= LangManager::translate('users.users.surname') ?>">
                </div>
            </div>
        </div>

        <div>
            <div class="card mb-4">
                <?php if ($user->useNativeLoginMethode()): ?>
                    <label for="passwordInput"><?= LangManager::translate('users.users.password') ?> :</label>
                    <div class="input-btn">
                        <input id="passwordInput" type="password" name="pass" class="form-control"
                               placeholder="••••••"/>
                        <button type="button" onclick="showPassword()"><i class="cursor-pointer fa fa-eye-slash"></i>
                        </button>
                    </div>
                    <label for="passwordInputV"><?= LangManager::translate('users.users.repeat_pass') ?> :</label>
                    <div class="input-btn">
                        <input id="passwordInputV" type="password" name="passVerif" class="form-control"
                               placeholder="••••••"/>
                        <button type="button" onclick="showPasswordV()"><i class="cursor-pointer fa fa-eye-slash"></i>
                        </button>
                    </div>
                <?php else: ?>
                    <p><?= LangManager::translate('users.users.login_methode') ?>
                        <b><?= ucfirst($user->getLoginMethode()) ?></b></p>
                <?php endif; ?>
            </div>
            <div class="card">
                <label for="roles">Rôles :</label>
                <select id="roles" class="choices choices__list--multiple" name="roles[]" multiple required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role->getId() ?>"
                            <?= (RolesModel::playerHasRole($user->getId(), $role->getId()) ? 'selected' : '') ?>><?= $role->getName() ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>
    <div class="card">
        <form action="../picture/edit/<?= $user->getId() ?>" method="post"
              enctype="multipart/form-data">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div class="grid-2">
                <img class="rounded-lg bg-contain"
                     src="<?= $user->getUserPicture()->getImage() ?>"
                     alt="<?= LangManager::translate('users.users.image.image_alt') . $user->getPseudo() ?>">
                <div class="drop-img-area" data-input-name="profilePicture"></div>
            </div>
            <div class="flex justify-between mt-2">
                <a href="../picture/reset/<?= $user->getId() ?>" type="submit" class="btn-warning"><i
                        class="fa fa-arrows-rotate"></i><?= LangManager::translate('users.users.image.reset') ?>
                </a>
                <button class="btn btn-primary" type="submit" id="profilePicture">
                    <?= LangManager::translate('core.btn.send') ?> <i class="fa-solid fa-upload"></i>
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    function showPassword() {
        let x = document.getElementById("passwordInput");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }

    function showPasswordV() {
        let x = document.getElementById("passwordInputV");
        if (x.type === "password") {
            x.type = "text";
        } else {
            x.type = "password";
        }
    }
</script>