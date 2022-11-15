<?php

use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\UsersSettingsModel;
use CMW\Utils\SecurityService;
use CMW\Utils\Utils;

/* @var \CMW\Model\Users\UsersSettingsModel $settings */

$title = LangManager::translate("users.settings.title");
$description = LangManager::translate("users.settings.desc"); ?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="" method="post" enctype="multipart/form-data">
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><?= LangManager::translate("users.settings.title") ?> :</h3>
                        </div>
                        <div class="card-body">

                            <!-- CONFIG SECTION -->

                            <img class="img-fluid" width="250px" height="250px"
                                 src="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') . 'public/uploads/users/default/' . UsersSettingsModel::getSetting("defaultImage") ?>">

                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="defaultPicture"
                                           accept=".png, .jpg, .jpeg, .webp, .gif"
                                           name="defaultPicture">
                                    <label class="custom-file-label" for="defaultPicture">
                                        <?= LangManager::translate("users.settings.default_picture") ?>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="resetPasswordMethod"><?= LangManager::translate("users.settings.resetPasswordMethod.label") ?></label>
                                <select class="form-control" id="resetPasswordMethod" name="resetPasswordMethod">

                                    <option value="0" <?= UsersSettingsModel::getSetting("resetPasswordMethod") === "0" ? 'selected' : '' ?>>
                                        <?= LangManager::translate("users.settings.resetPasswordMethod.options.0") ?>
                                    </option>

                                    <option value="1" <?= UsersSettingsModel::getSetting("resetPasswordMethod") === "1" ? 'selected' : '' ?>>
                                        <?= LangManager::translate("users.settings.resetPasswordMethod.options.1") ?>
                                    </option>

                                </select>
                            </div>


                        </div>
                        <div class="card-footer">
                            <button type="submit"
                                    class="btn btn-primary float-right"><?= LangManager::translate("core.btn.save", lineBreak: true) ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>