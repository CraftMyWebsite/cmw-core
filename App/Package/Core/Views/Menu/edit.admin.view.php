<?php

use CMW\Entity\Users\RoleEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\MenusModel;

/* @var array $packagesLinks */
/* @var RoleEntity[] $roles */
/* @var \CMW\Entity\Core\MenuEntity $instanceMenu */

$title = LangManager::translate("core.menus.title");
$description = LangManager::translate("core.menus.desc");
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fas fa-bars"></i> <span class="m-lg-auto">Édition du menu <?= $instanceMenu->getName() ?></span>
    </h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body">

                <form action="" method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("core.menus.add.name") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" name="name" class="form-control" value="<?= $instanceMenu->getName() ?>"
                                       placeholder="<?= LangManager::translate("core.menus.add.name_hint") ?>"
                                       required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-text-width"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="targetBlank"
                                       id="targetBlank" <?= $instanceMenu->isTargetBlank() ? 'checked' : '' ?>>
                                <label class="form-check-label"
                                       for="targetBlank"><?= LangManager::translate("core.menus.add.targetBlank") ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2 align-items-center">
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("core.menus.add.choice") ?> :</h6>
                            <div class="form-group">
                                <select class="choices form-select super-choice" name="choice" required>
                                    <option value="package">
                                        <?= LangManager::translate("core.menus.add.package") ?>
                                    </option>
                                    <option value="custom">
                                        <?= LangManager::translate("core.menus.add.custom") ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <!-- Dynamic input type -->
                        <div class="col-md-6 addPackage">
                            <h6><?= LangManager::translate("core.menus.add.package_select") ?> :</h6>
                            <div class="form-group">
                                <select class="choices form-select" name="slugPackage">
                                    <?php foreach ($packagesLinks as $package => $routes):
                                        if ($routes !== []):?>
                                            <option disabled>──── <?= $package ?> ────</option>
                                        <?php endif; ?>
                                        <?php foreach ($routes as $name => $route): ?>
                                        <option value="<?= $route ?>"><?= $name ?></option>
                                    <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 d-none addCustom">
                            <h6><?= LangManager::translate("core.menus.add.custom") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" name="slugCustom" class="form-control"
                                       placeholder="<?= LangManager::translate("core.menus.add.custom_hint") ?>">
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-link"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-6 mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input allowedGroups" id="allowedGroups" type="checkbox"
                                       name="allowedGroupsToggle" <?= $instanceMenu->isRestricted() ? 'checked' : '' ?>
                                >
                                <label class="form-check-label" for="allowedGroups">
                                    <?= LangManager::translate("core.menus.add.allowedGroups") ?>
                                </label>
                            </div>
                        </div>
                        <!-- List groups -->
                        <div class="col-md-6 mt-2" id="listAllowedGroups">
                            <h6><?= LangManager::translate("core.menus.add.group_select") ?> :</h6>
                            <div class="form-group">
                                <select class="choices form-select" name="allowedGroups[]" multiple>
                                    <?php foreach ($roles as $role): ?>
                                        <option
                                            <?php foreach (MenusModel::getInstance()->getAllowedRoles($instanceMenu->getId()) as $allowedRoles): ?>
                                                <?= $allowedRoles->getName() === $role->getName() ? 'selected' : '' ?>
                                            <?php endforeach ?>

                                            value="<?= $role->getId() ?>"><?= $role->getName() ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check"></i>
                            <span class=""><?= LangManager::translate("core.btn.edit") ?></span>
                        </button>
                    </div>

                </form>
            </div>


        </div>
    </div>
</section>
