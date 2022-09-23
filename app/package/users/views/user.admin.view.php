<?php

use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\RolesModel;

$title = LangManager::translate("users.edit.title");
$description = LangManager::translate("users.edit.desc");

$scripts = '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/js/main.js"></script>';

/** @var \CMW\Entity\Users\UserEntity $user */
?>

<!-- main-content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <form action="" method="post">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?= LangManager::translate("users.users.user") ?> : <?= $user->getUsername() ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" name="email" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.mail") ?>" value="<?= $user->getMail() ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                </div>
                                <input type="text" name="pseudo" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.pseudo") ?>" value="<?= $user->getUsername() ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input type="text" name="name" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.firstname") ?>" value="<?= $user->getFirstName() ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input type="text" name="lastname" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.surname") ?>" value="<?= $user->getLastName() ?>">
                            </div>
                            <div class="form-group">
                                <label><?= LangManager::translate("users.users.role") ?></label>
                                <select name="roles[]" class="form-control" multiple>
                                    <?php /** @var \CMW\Entity\Users\RoleEntity[] $roles */
                                    foreach ($roles as $role) : ?>
                                        <option value="<?= $role->getId() ?>"
                                            <?= (RolesModel::playerHasRole($user->getId(), $role->getId()) ? "selected" : "") ?>><?= $role->getName() ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?= LangManager::translate("users.users.new_pass") ?></label>
                                <div class="input-group mb-3" id="showHidePassword">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" name="pass" class="form-control" placeholder="******">
                                    <div class="input-group-append">
                                        <a class="input-group-text" href="#"><i class="fa fa-eye-slash"
                                                                                aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?= LangManager::translate("users.users.repeat_pass") ?></label>
                                <div class="input-group mb-3" id="showHidePasswordR">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    </div>
                                    <input type="password" name="pass_verif" class="form-control"
                                           placeholder="*****">
                                    <div class="input-group-append">
                                        <a class="input-group-text" href="#"><i class="fa fa-eye-slash"
                                                                                aria-hidden="true"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit"
                                    class="btn btn-primary float-right"><?= LangManager::translate("core.btn.save") ?></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><?= LangManager::translate("users.users.about") ?></h3>
                    </div>
                    <div class="card-body">
                        <p><b><?= LangManager::translate("users.users.creation") ?> :</b> <?= $user->getCreated() ?></p>
                        <p><b><?= LangManager::translate("users.users.last_edit") ?> :</b> <?= $user->getUpdated() ?></p>
                        <p><b><?= LangManager::translate("users.users.last_connection") ?> :</b> <?= $user->getLastConnection() ?></p>
                        <div>
                            <a href="../state/<?= $user->getId() ?>/<?= $user->getState() ?>" type="submit"
                               class="btn btn-<?= ($user->getState()) ? 'warning' : 'success' ?>"><i
                                        class="fa fa-user-slash"></i> <?= ($user->getState()) ? LangManager::translate("users.edit.disable_account") : LangManager::translate("users.edit.activate_account") ?>
                            </a>

                            <a href="../delete/<?= $user->getId() ?>" type="submit" class="btn btn-danger"><i
                                        class="fa fa-user-times"></i> <?= LangManager::translate("core.btn.delete") ?>
                            </a>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
</div>
<!-- /.main-content -->