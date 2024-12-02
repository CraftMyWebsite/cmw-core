<?php

use CMW\Entity\Users\RoleEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MenusModel;

/* @var array $packagesLinks */
/* @var RoleEntity[] $roles */
/* @var \CMW\Entity\Core\MenuEntity $instanceMenu */

$title = LangManager::translate('core.menus.title');
$description = LangManager::translate('core.menus.desc');
?>

<h3><i class="fas fa-bars"></i> <?= LangManager::translate('core.menus.add.sub_menu') ?> <?= $instanceMenu->getName() ?></h3>

<div class="center-flex">
    <div class="flex-content-lg card">
        <form action="" method="post" class="space-y-2">
            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
            <div>
                <label for="name"><?= LangManager::translate('core.menus.add.name') ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-text-width"></i>
                    <input type="text" id="name" name="name" value="<?= $instanceMenu->getName() ?>" placeholder="<?= LangManager::translate('core.menus.add.name_hint') ?>"
                           required>
                </div>
            </div>
            <div>
                <label for="super-choice"><?= LangManager::translate('core.menus.add.choice') ?> :</label>
                <select class="choices" id="super-choice" name="choice" required>
                    <option <?= $instanceMenu->isCustomUrl() ? '' : 'selected' ?> value="package">
                        <?= LangManager::translate('core.menus.add.package') ?>
                    </option>
                    <option <?= $instanceMenu->isCustomUrl() ? 'selected' : '' ?> value="custom">
                        <?= LangManager::translate('core.menus.add.custom') ?>
                    </option>
                </select>
            </div>
            <div id="addPackage">
                <label for="slugPackage"><?= LangManager::translate('core.menus.add.package_select') ?> :</label>
                <select id="slugPackage" class="choices" name="slugPackage">
                    <?php foreach ($packagesLinks as $package => $routes):
                        if ($routes !== []): ?>
                            <option disabled>──── <?= $package ?> ────</option>
                        <?php endif; ?>
                        <?php foreach ($routes as $name => $route): ?>
                        <option <?= $instanceMenu->getUnformatedUrl() === $route ? 'selected' : '' ?> value="<?= $route ?>"><?= $name ?></option>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="addCustom" style="display: none">
                <label for="slugCustom"><?= LangManager::translate('core.menus.add.custom') ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-link"></i>
                    <input type="text" value="<?= $instanceMenu->getUrl() ?>" id="slugCustom" name="slugCustom" class="form-control"
                           placeholder="<?= LangManager::translate('core.menus.add.custom_hint') ?>">
                </div>
            </div>
            <div>
                <label class="toggle">
                    <p class="toggle-label"><?= LangManager::translate('core.menus.add.targetBlank') ?></p>
                    <input type="checkbox" class="toggle-input" name="targetBlank" id="targetBlank" <?= $instanceMenu->isTargetBlank() ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                </label>
            </div>
            <div>
                <label class="toggle">
                    <p class="toggle-label"><?= LangManager::translate('core.menus.add.allowedGroups') ?> :</p>
                    <input type="checkbox" class="toggle-input" <?= $instanceMenu->isRestricted() ? 'checked' : '' ?> name="allowedGroupsToggle" id="allowedGroups">
                    <div class="toggle-slider"></div>
                </label>
            </div>
            <div id="listAllowedGroups" style="display: none">
                <label for="allowedGroups"></label>
                <select class="choices form-select" id="allowedGroups" name="allowedGroups[]" multiple>
                    <?php foreach ($roles as $role): ?>
                        <option
                            <?php foreach (MenusModel::getInstance()->getAllowedRoles($instanceMenu->getId()) as $allowedRoles): ?>
                                <?= $allowedRoles->getName() === $role->getName() ? 'selected' : '' ?>
                            <?php endforeach ?>
                            value="<?= $role->getId() ?>"><?= $role->getName() ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-primary btn-center"><?= LangManager::translate('core.btn.edit') ?></button>
        </form>
    </div>
</div>