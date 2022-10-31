<?php

use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\RolesModel;
use CMW\Utils\SecurityService;
use CMW\Utils\Utils;

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
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?= LangManager::translate("users.users.user") ?>
                                : <?= $user->getUsername() ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" name="email" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.mail") ?>"
                                       value="<?= $user->getMail() ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                </div>
                                <input type="text" name="pseudo" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.pseudo") ?>"
                                       value="<?= $user->getUsername() ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input type="text" name="name" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.firstname") ?>"
                                       value="<?= $user->getFirstName() ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input type="text" name="lastname" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.surname") ?>"
                                       value="<?= $user->getLastName() ?>">
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
                                    class="btn btn-primary float-right"><?= LangManager::translate("core.btn.save") ?>
                            </button>
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
                        <p><b><?= LangManager::translate("users.users.last_edit") ?> :</b> <?= $user->getUpdated() ?>
                        </p>
                        <p><b><?= LangManager::translate("users.users.last_connection") ?>
                                :</b> <?= $user->getLastConnection() ?></p>
                        <div class="row">
                            <a href="../state/<?= $user->getId() ?>/<?= $user->getState() ?>" type="submit"
                               class="btn btn-<?= ($user->getState()) ? 'warning' : 'success' ?>"><i
                                        class="fa fa-user-slash"></i> <?= ($user->getState()) ? LangManager::translate("users.edit.disable_account") : LangManager::translate("users.edit.activate_account") ?>
                            </a>

                            <div class="ml-3">
                                <a href="../delete/<?= $user->getId() ?>" type="submit" class="btn btn-danger"><i
                                            class="fa fa-user-times"></i> <?= LangManager::translate("core.btn.delete") ?>
                                </a>
                            </div>

                            <!-- RESET PASSWORD -->
                            <div class="ml-3">
                                <form method="post"
                                      action="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>login/forgot">
                                    <?php (new SecurityService())->insertHiddenToken() ?>
                                    <input type="hidden" value="<?= $user->getMail() ?>" name="mail">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fa fa-arrows-rotate"></i>
                                        <?= LangManager::translate("users.edit.reset_password") ?>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                    <!-- /.card-body    -->
                </div>

                <!-- Edit profile picture -->
                <div>
                    <form action="../picture/edit/<?= $user->getId() ?>" method="post" enctype="multipart/form-data">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title"><?= LangManager::translate("users.users.image.title") ?></h3>
                            </div>
                            <div class="card-body">
                                <p><b><?= LangManager::translate("users.users.image.last_update") ?>:</b>
                                    <?= ($user->getUserPicture()->getLastUpdate() ?? $user->getCreated()) ?>
                                </p>

                                <div class="container">
                                    <div class="row mx-auto">
                                        <div class="col-4">
                                            <img src="<?= getenv('PATH_SUBFOLDER') ?>public/uploads/users/<?= $user->getUserPicture()->getImageName() ?>"
                                                 height="150px" width="150px"
                                                 alt="<?= LangManager::translate("users.users.image.image_alt")
                                                 . $user->getUsername() ?>">
                                        </div>
                                        <div class="input-group col">
                                            <div class="input-group col">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="profilePicture"
                                                           accept=".png, .jpg, .jpeg, .webp, .gif"
                                                           name="profilePicture">
                                                    <label class="custom-file-label" for="profilePicture">
                                                        <?= LangManager::translate("users.users.image.placeholder_input") ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="w-100"></div>
                                            <div class="col">
                                                <a href="../picture/reset/<?= $user->getId() ?>" type="submit"
                                                   class="btn btn-warning"><i
                                                            class="fa fa-arrows-rotate"></i>
                                                    <?= LangManager::translate("users.users.image.reset") ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit"
                                        class="btn btn-primary float-left"><?= LangManager::translate("core.btn.save") ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
</div>
<!-- /.main-content -->