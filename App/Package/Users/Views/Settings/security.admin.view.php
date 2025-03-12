<?php

use CMW\Entity\Users\RoleEntity;
use CMW\Entity\Users\UserSettingsEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Users\UsersSettingsModel;

/* @var UserSettingsEntity $settings */
/* @var RoleEntity[] $roles */

$title = LangManager::translate('users.settings.title');
$description = LangManager::translate('users.settings.desc');
?>

<div class="page-title">
    <h3>
        <i class="fa-solid fa-gears"></i> <?= LangManager::translate('users.settings.title') ?>
        - <?= LangManager::translate('users.pages.settings.security.menu') ?>
    </h3>

    <button form="security" type="submit" class="btn btn-primary">
        <?= LangManager::translate('core.btn.save') ?>
    </button>
</div>

<form method="post" id="security">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>

    <div class="h-fit card space-y-4">
        <div>
            <label><?= LangManager::translate('users.settings.resetPasswordMethod.label') ?></label>
            <select id="basicSelect" name="reset_password_method" required>
                <option value="0" <?= $settings->getResetPasswordMethod() === 0 ? 'selected' : '' ?>>
                    <?= LangManager::translate('users.settings.resetPasswordMethod.options.0') ?>
                </option>
                <option value="1" <?= $settings->getResetPasswordMethod() === 1 ? 'selected' : '' ?>>
                    <?= LangManager::translate('users.settings.resetPasswordMethod.options.1') ?>
                </option>
            </select>
        </div>
        <div>
            <label for="security_reinforced"><?= LangManager::translate('users.long_date.setting.label') ?></label>
            <select class="form-select" id="security_reinforced" name="security_reinforced" required>
                <option
                    value="0" <?= UsersSettingsModel::getInstance()->getSetting('securityReinforced') === '0' ? 'selected' : '' ?>>
                    <?= LangManager::translate('users.long_date.setting.no') ?>
                </option>
                <option
                    value="1" <?= UsersSettingsModel::getInstance()->getSetting('securityReinforced') === '1' ? 'selected' : '' ?>>
                    <?= LangManager::translate('users.long_date.setting.yes') ?>
                </option>
            </select>
            <small><?= LangManager::translate('users.long_date.setting.small') ?></small>
        </div>

        <div>
            <label>Double facteur obligatoire</label>
            <fieldset class="form-group">
                <select class="form-select" id="listEnforcedToggle" name="listEnforcedToggle" required>
                    <option
                        value="0" <?= !UsersSettingsModel::getInstance()->getSetting('listEnforcedToggle') ? 'selected' : '' ?>>
                        Pas d'obligation
                    </option>
                    <option
                        value="1" <?= UsersSettingsModel::getInstance()->getSetting('listEnforcedToggle') ? 'selected' : '' ?>>
                        Ayant le rôle :
                    </option>
                </select>
            </fieldset>
            <div class="mt-2" id="listEnforcedRoles">
                <h6><?= LangManager::translate('core.menus.add.group_select') ?> :</h6>
                <div class="form-group">
                    <select class="choices form-select" name="enforcedRoles[]" multiple>
                        <?php foreach ($roles as $role): ?>
                            <option
                                <?= in_array($role->getId(), array_map(static fn($r) => $r->getRole()->getId(), UsersSettingsModel::getInstance()->getEnforcedRoles()), true) ? 'selected' : '' ?>
                                value="<?= $role->getId() ?>">
                                <?= $role->getName() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    const basicSelect = document.getElementById('listEnforcedToggle');
    const listAllowedGroups = document.getElementById('listEnforcedRoles');

    basicSelect.addEventListener('change', function () {
        if (basicSelect.value === "1") {
            listAllowedGroups.style.display = 'block';
        } else {
            listAllowedGroups.style.display = 'none';
        }
    });

    if (basicSelect.value === "1") {
        listAllowedGroups.style.display = 'block';
    } else {
        listAllowedGroups.style.display = 'none';
    }
</script>
