<?php

use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("users.add.title");
$description = LangManager::translate("users.add.desc");

$scripts = '<script src="' . getenv("PATH_SUBFOLDER") . 'admin/resources/js/main.js"></script>';

/* @var \CMW\Entity\Roles\RoleEntity[] $roles */
?>

<!-- main-content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="" method="post">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?= LangManager::translate("users.add.card_title") ?> :</h3>
                        </div>
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" name="email" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.mail") ?>" required>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                </div>
                                <input type="text" name="pseudo" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.pseudo") ?>" required>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input type="text" name="name" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.firstname") ?>">
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                </div>
                                <input type="text" name="lastname" class="form-control"
                                       placeholder="<?= LangManager::translate("users.users.surname") ?>">
                            </div>
                            <div class="form-group">
                                <label><?= LangManager::translate("users.users.role") ?></label>
                                <select name="roles[]" class="form-control" multiple>
                                    <?php
                                    foreach ($roles as $role) : ?>
                                        <option value="<?= $role->getId() ?>"><?= $role->getName() ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?= LangManager::translate("users.users.pass") ?></label>
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
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit"
                                    class="btn btn-primary float-right"><?= LangManager::translate("core.btn.save") ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.row -->
    </div>
</div>
<!-- /.main-content -->