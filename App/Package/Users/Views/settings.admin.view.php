<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\Users\UserSettingsEntity $settings */
/* @var RoleEntity[] $roles */

$title = LangManager::translate("users.settings.title");
$description = LangManager::translate("users.settings.desc"); ?>

<form action="" method="post" enctype="multipart/form-data">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <div class="d-flex flex-wrap justify-content-between">
        <h3><i class="fa-solid fa-gears"></i> <span
                    class="m-lg-auto"><?= LangManager::translate("users.settings.title") ?></span></h3>
        <div class="buttons">
            <button type="submit" class="btn btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
        </div>
    </div>
    <section class="row">

        <!-- Default picture -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4><?= LangManager::translate("users.settings.visualIdentity") ?></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <h6><?= LangManager::translate("users.settings.default_picture") ?> :</h6>
                        <div class="text-center ">
                            <img class="w-25 border"
                                 src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Uploads/Users/Default/<?= $settings->getDefaultImage() ?>"
                                 alt="<?= LangManager::translate("users.settings.default_picture") ?>">
                        </div>
                        <input class="mt-2 form-control form-control-lg" type="file" id="formFile"
                               accept=".png, .jpg, .jpeg, .webp, .gif"
                               name="defaultPicture">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <!-- Reset password -->
            <div class="card">
                <div class="card-header">
                    <h4><?= LangManager::translate("users.users.password") ?>
                        <i data-bs-toggle="tooltip"
                           title="<?= LangManager::translate('users.settings.resetPasswordMethod.tips') ?>"
                           class="fa-sharp fa-solid fa-circle-question">
                        </i>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <h6><?= LangManager::translate('users.settings.resetPasswordMethod.label') ?> :</h6>
                        <fieldset class="form-group">
                            <select class="form-select" id="basicSelect" name="reset_password_method"  required>
                                <option value="0" <?= $settings->getResetPasswordMethod() === 0 ? 'selected' : '' ?>>
                                    <?= LangManager::translate("users.settings.resetPasswordMethod.options.0") ?>
                                </option>

                                <!--<option value="1" <?= $settings->getResetPasswordMethod() === 1 ? 'selected' : '' ?>>
                                    <?= LangManager::translate("users.settings.resetPasswordMethod.options.1") ?>
                                </option>-->
                            </select>
                        </fieldset>
                    </div>
                </div>
            </div>

            <!-- 2fa -->
            <div class="card">
                <div class="card-header">
                    <h4>Double facteur</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <h6>Obligatoire pour :</h6>
                        <fieldset class="form-group">
                            <select class="form-select" id="forceTo" name="forceTo"  required>
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
            </div>
        </div>

        <!-- /profile view -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4><?= LangManager::translate("users.settings.profile_view.title") ?>
                        <i data-bs-toggle="tooltip"
                           title="<?= LangManager::translate('users.settings.profile_view.tips') ?>"
                           class="fa-sharp fa-solid fa-circle-question">
                        </i>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <h6><?= LangManager::translate('users.settings.profile_view.label') ?> :</h6>
                        <fieldset class="form-group">
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
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pseudo blacklist -->
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4><?= LangManager::translate('users.settings.blacklisted.pseudo.label') ?>
                        <i data-bs-toggle="tooltip"
                           title="<?= LangManager::translate('users.settings.blacklisted.pseudo.hint') ?>"
                           class="fa-sharp fa-solid fa-circle-question">
                        </i>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <a href="settings/blacklist/pseudo" class="btn btn-primary"><?= LangManager::translate('users.settings.blacklisted.pseudo.goBtn') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</form>


<script>
    var basicSelect = document.getElementById('forceTo');
    var listAllowedGroups = document.getElementById('listAllowedGroups');

    basicSelect.addEventListener('change', function() {
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





