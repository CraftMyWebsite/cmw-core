<?php

use CMW\Entity\Users\UserSettingsEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var UserSettingsEntity $settings */

$title = LangManager::translate('users.settings.title');
$description = LangManager::translate('users.settings.desc');
?>

<div class="page-title">
    <h3>
        <i class="fa-solid fa-gears"></i> <?= LangManager::translate('users.settings.title') ?>
        - <?= LangManager::translate('users.pages.settings.general.menu') ?>
    </h3>
</div>

<div class="grid-2">
    <div class="card mb-4">
        <form id="image" action="general/image" method="post" enctype="multipart/form-data">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div class="card-title">
                <h6><?= LangManager::translate('users.settings.default_picture') ?></h6>
                <a href="general/image/reset" class="btn-warning">
                    <?= LangManager::translate('core.theme.reset') ?>
                </a>
            </div>
            <div class="grid-2">
                <img class="rounded-lg"
                     src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Uploads/Users/Default/<?= $settings->getDefaultImage() ?>"
                     alt="<?= LangManager::translate('users.settings.default_picture') ?>">
                <div class="drop-img-area" data-input-name="defaultPicture"></div>
            </div>

            <div class="pt-2 float-end">
                <button form="image" type="submit" class="btn btn-primary">
                    <?= LangManager::translate('core.btn.save') ?>
                </button>
            </div>
        </form>
    </div>

    <div class="card">
        <form method="post" id="general">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>

            <label><?= LangManager::translate('users.settings.profile_view.label') ?></label>
            <select class="form-select" id="basicSelect" name="profile_page" required>
                <option value="0" <?= $settings->getProfilePageStatus() === 0 ? 'selected' : '' ?>>
                    <?= LangManager::translate('users.settings.profile_view.options.0') ?>
                </option>

                <option value="1" <?= $settings->getProfilePageStatus() === 1 ? 'selected' : '' ?>>
                    <?= LangManager::translate('users.settings.profile_view.options.1') ?>
                </option>

                <option value="2" <?= $settings->getProfilePageStatus() === 2 ? 'selected' : '' ?>>
                    <?= LangManager::translate('users.settings.profile_view.options.2') ?>
                </option>
            </select>

            <div class="pt-2 float-end">
                <button form="general" type="submit" class="btn btn-primary">
                    <?= LangManager::translate('core.btn.save') ?>
                </button>
            </div>
        </form>
    </div>
</div>
