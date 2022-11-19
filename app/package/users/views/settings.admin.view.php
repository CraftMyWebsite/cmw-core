<?php

use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\SecurityService;
use CMW\Utils\Utils;

/* @var \CMW\Model\Users\UsersSettingsModel $settings */

$title = LangManager::translate("users.settings.title");
$description = LangManager::translate("users.settings.desc"); ?>

<form action="" method="post" enctype="multipart/form-data">
    <div class="d-flex flex-wrap justify-content-between">
        <h3><i class="fa-solid fa-gears"></i> <span
                    class="m-lg-auto"><?= LangManager::translate("users.settings.title") ?></span></h3>
        <div class="buttons">
            <button type="submit" class="btn btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
        </div>
    </div>
    <section class="row">
        <div class="col-12 col-lg-6">
            <?php (new SecurityService())->insertHiddenToken() ?>
            <div class="card">
                <div class="card-header">
                    <h4><?= LangManager::translate("users.settings.visualIdentity") ?></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <h6><?= LangManager::translate("users.settings.default_picture") ?> :</h6>
                        <div class="text-center ">
                            <img class="w-25 border"
                                 src="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>public/uploads/users/default/<?= UsersSettingsModel::getSetting("defaultImage") ?>"
                                 alt="<?= LangManager::translate("users.settings.default_picture") ?>">
                        </div>
                        <input class="mt-2 form-control form-control-lg" type="file" id="formFile"
                               accept=".png, .jpg, .jpeg, .webp, .gif"
                               name="defaultPicture">
                    </div>
                </div>
            </div>
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
                            <select class="form-select" id="basicSelect" name="resetPasswordMethod" required>
                                <option value="0" <?= UsersSettingsModel::getSetting("resetPasswordMethod") === "0" ? 'selected' : '' ?>>
                                    <?= LangManager::translate("users.settings.resetPasswordMethod.options.0") ?>
                                </option>

                                <option value="1" <?= UsersSettingsModel::getSetting("resetPasswordMethod") === "1" ? 'selected' : '' ?>>
                                    <?= LangManager::translate("users.settings.resetPasswordMethod.options.1") ?>
                                </option>
                            </select>
                        </fieldset>
                    </div>

                </div>
    </section>
</form>