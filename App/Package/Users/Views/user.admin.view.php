<?php

use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\RolesModel;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Utils;

$title = LangManager::translate("users.edit.title");
$description = LangManager::translate("users.edit.desc");

$scripts = '<script src="' . getenv("PATH_SUBFOLDER") . 'Admin/Resources/Js/main.js"></script>';

/** @var \CMW\Entity\Users\UserEntity $user */
?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gears"></i> <span class="m-lg-auto">Édition de : <?= $user->getPseudo() ?></span></h3>
</div>

<section class="row">
    <div class="col-md-6">
        <!--UTILISATEUR-->
        <div class="card">
            <div class="card-header">
                <h4><?= LangManager::translate("users.users.user") ?></h4>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("users.users.mail") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="email" name="email" class="form-control" value="<?= $user->getMail() ?>" placeholder="<?= LangManager::translate("users.users.mail") ?>" required>
                                <div class="form-control-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("users.users.pseudo") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" name="pseudo" class="form-control" value="<?= $user->getPseudo() ?>" placeholder="<?= LangManager::translate("users.users.pseudo") ?>" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-signature"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("users.users.firstname") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" name="name" class="form-control" value="<?= $user->getFirstName() ?>" placeholder="<?= LangManager::translate("users.users.firstname") ?>" >
                                <div class="form-control-icon">
                                    <i class="fas fa-id-card"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("users.users.surname") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" name="lastname" class="form-control" value="<?= $user->getLastName() ?>" placeholder="<?= LangManager::translate("users.users.surname") ?>" >
                                <div class="form-control-icon">
                                    <i class="fas fa-id-card"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("users.users.password") ?> :</h6>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="passwordInput" type="password" name="pass" class="form-control" placeholder="••••••">
                                <button onclick="showPassword()" class="btn btn-primary" type="button">
                                  <i class="cursor-pointer fa fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("users.users.repeat_pass") ?> :</h6>
                            <div class="input-group mb-3">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input id="passwordInputV" type="password" name="passVerif" class="form-control" placeholder="••••••">
                                <button onclick="showPasswordV()" class="btn btn-primary" type="button">
                                  <i class="cursor-pointer fa fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <h6>Rôles (valeur multiple possible) :</h6>
                    <fieldset class="form-group">
                        <select class="choices choices__list--multiple" name="roles[]" multiple required>
                            <?php foreach ($roles as $role) : ?>
                                <option value="<?= $role->getId() ?>"
                                            <?= (RolesModel::playerHasRole($user->getId(), $role->getId()) ? "selected" : "") ?>><?= $role->getName() ?>
                                        </option>
                            <?php endforeach; ?>
                        </select>
                    </fieldset>

                    <div class="buttons text-center">
                        <button type="submit" class="btn btn-primary"><?= LangManager::translate("core.btn.save", lineBreak: true) ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <!--A PROPOS-->
        <div class="card">
            <div class="card-header">
                <h4><?= LangManager::translate("users.users.about") ?></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6><?= LangManager::translate("users.users.creation") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" value="<?= $user->getCreated() ?>" disabled>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-circle-plus"></i>
                                </div>
                            </div>
                    </div>
                    <div class="col-md-4">
                        <h6><?= LangManager::translate("users.users.last_edit") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" value="<?= $user->getUpdated() ?>" disabled>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </div>
                            </div>
                    </div>
                    <div class="col-md-4">
                        <h6><?= LangManager::translate("users.users.last_connection") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" value="<?= $user->getLastConnection() ?>" disabled>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                </div>
                            </div>
                    </div>
                </div>

                <div class="row text-center mt-2">
                    <div class="col-md-6">
                        <a href="../state/<?= $user->getId() ?>/<?= $user->getState() ?>" type="submit"class="btn btn-<?= ($user->getState()) ? 'warning' : 'success' ?>"><i class="fa fa-user-slash"></i>  <?= ($user->getState()) ? LangManager::translate("users.edit.disable_account") : LangManager::translate("users.edit.activate_account") ?></a>
                    </div>
                    <div class="col-md-6">
                        <div class="ml-3">
                            <form method="post" action="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>login/forgot">
                                <?php (new SecurityManager())->insertHiddenToken() ?>
                                <input type="hidden" value="<?= $user->getMail() ?>" name="mail">
                                <button type="submit" class="btn btn-warning">
                                    <i class="fa fa-arrows-rotate"></i>
                                    <?= LangManager::translate("users.edit.reset_password") ?>
                                </button>
                                </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--IMAGES-->
        <div class="card">
            <div class="card-header">
                <h4><?= LangManager::translate("users.users.image.title") ?></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-center ">
                            <img class="w-50" src="<?= getenv('PATH_SUBFOLDER') ?>public/uploads/users/<?= $user->getUserPicture()->getImageName() ?>" alt="<?= LangManager::translate("users.users.image.image_alt"). $user->getPseudo() ?>">
                        </div>
                        <form action="../picture/edit/<?= $user->getId() ?>" method="post" enctype="multipart/form-data">
                            <?php (new SecurityManager())->insertHiddenToken() ?>
                            <div class="input-group mt-1">
                                <input type="file" class="form-control" id="profilePicture" name="profilePicture" accept=".png, .jpg, .jpeg, .webp, .gif">
                                <button class="btn btn-primary" type="submit" id="profilePicture">
                                    <i class="fa-solid fa-upload"></i>
                                </button>
                            </div>
                            <span>Fichiers autorisé ; png, jpg, jpeg, webp, svg, gif</span>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <h6><?= LangManager::translate("users.users.image.last_update") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" class="form-control" value="<?= ($user->getUserPicture()->getLastUpdate() ?? $user->getCreated()) ?>" disabled>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </div>
                            </div>
                        <div class="buttons text-center">
                            <a href="../picture/reset/<?= $user->getId() ?>" type="submit" class="btn btn-warning"><i class="fa fa-arrows-rotate"></i><?= LangManager::translate("users.users.image.reset") ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<script>
function showPassword() {
  var x = document.getElementById("passwordInput");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
function showPasswordV() {
  var x = document.getElementById("passwordInputV");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
</script>