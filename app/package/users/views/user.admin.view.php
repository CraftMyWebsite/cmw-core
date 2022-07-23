<?php

use CMW\Model\Roles\RolesModel;

$title = USERS_EDIT_TITLE;
$description = USERS_EDIT_DESC;

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
                            <h3 class="card-title"><?= USERS_USER ?> : <?= $user->getUsername() ?></h3>
                        </div>
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" name="email" class="form-control"
                                       placeholder="<?= USERS_MAIL ?>" value="<?= $user->getMail() ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                </div>
                                <input type="text" name="pseudo" class="form-control"
                                       placeholder="<?= USERS_PSEUDO ?>" value="<?= $user->getUsername() ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input type="text" name="name" class="form-control"
                                       placeholder="<?= USERS_FIRSTNAME ?>" value="<?= $user->getFirstName() ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input type="text" name="lastname" class="form-control"
                                       placeholder="<?= USERS_SURNAME ?>" value="<?= $user->getLastName() ?>">
                            </div>
                            <div class="form-group">
                                <label><?= USERS_ROLE ?></label>
                                <select name="roles[]" class="form-control" multiple>
                                    <?php /** @var \CMW\Entity\Roles\RoleEntity[] $roles */
                                    foreach ($roles as $role) : ?>
                                        <option value="<?= $role->getId() ?>"
                                            <?= (RolesModel::playerHasRole($user->getId(), $role->getId()) ? "selected" : "") ?>><?= $role->getName() ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?= USERS_NEW_PASS ?></label>
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
                                <label><?= USERS_REPEAT_PASS ?></label>
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
                                    class="btn btn-primary float-right"><?= USERS_LIST_BUTTON_SAVE ?></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title"><?= USERS_ABOUT ?></h3>
                    </div>
                    <div class="card-body">
                        <p><b><?= USERS_CREATION ?> :</b> <?= $user->getCreated() ?></p>
                        <p><b><?= USERS_LAST_EDIT ?> :</b> <?= $user->getUpdated() ?></p>
                        <p><b><?= USERS_LAST_CONNECTION ?> :</b> <?= $user->getLastConnection() ?></p>
                        <div>
                            <a href="../state/<?= $user->getId() ?>/<?= $user->getState() ?>" type="submit"
                               class="btn btn-<?= ($user->getState()) ? 'warning' : 'success' ?>"><i
                                        class="fa fa-user-slash"></i> <?= ($user->getState()) ? USERS_EDIT_DISABLE_ACCOUNT : USERS_EDIT_ACTIVATE_ACCOUNT ?>
                            </a>

                            <a href="../delete/<?= $user->getId() ?>" type="submit" class="btn btn-danger"><i
                                        class="fa fa-user-times"></i> <?= USERS_EDIT_DELETE_ACCOUNT ?>
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