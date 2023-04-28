<?php

use CMW\Entity\Users\RoleEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var array $packagesLinks */
/* @var RoleEntity[] $roles */
/* @var \CMW\Entity\Core\MenuEntity[] $menus */

$title = LangManager::translate("core.menus.title");
$description = LangManager::translate("core.menus.desc");
?>

<!-- main-content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Menu principal</h3>
                    </div>

                    <div class="card-footer">

                        <a class="btn btn-primary collapsed float-left" data-bs-toggle="collapse"
                           href="#addMenu" role="button" aria-expanded="false" aria-controls="addMenu">
                            Ajouter un menu classique
                        </a>

                        <button class="btn btn-primary float-right" disabled>
                            Ajouter un menu dropdown
                        </button>


                        <!-- Collapse add menu -->
                        <div class="collapse mt-4" id="addMenu" style="">
                            <form action="" method="post">
                                <?php (new SecurityManager())->insertHiddenToken() ?>
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <h6><?= LangManager::translate("core.menus.add.name") ?> :</h6>
                                        <div class="form-group position-relative has-icon-left">
                                            <input type="text" name="name" class="form-control"
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
                                                   id="targetBlank">
                                            <label class="form-check-label"
                                                   for="targetBlank"><?= LangManager::translate("core.menus.add.targetBlank") ?></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2 align-items-center">
                                    <div class="col-md-6">
                                        <h6><?= LangManager::translate("core.menus.add.choice") ?> :</h6>
                                        <div class="form-group">
                                            <select class="choices form-select" name="choice" id="choice" required>
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
                                    <div class="col-md-6" id="addPackage">
                                        <h6><?= LangManager::translate("core.menus.add.package_select") ?> :</h6>
                                        <div class="form-group">
                                            <select class="choices form-select" name="slugPackage">
                                                <?php foreach ($packagesLinks as $package => $routes): ?>
                                                    <option disabled>────────── <?= $package ?> ──────────</option>
                                                    <?php foreach ($routes as $route): ?>
                                                        <option value="<?= $route ?>"><?= $route ?></option>
                                                    <?php endforeach; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 d-none" id="addCustom">
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


                                <!-- Group allowed -->

                                <div class="row align-items-center">

                                    <div class="col-md-6 mt-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="allowedGroupsToggle"
                                                   id="allowedGroups">
                                            <label class="form-check-label" for="allowedGroups">
                                                <?= LangManager::translate("core.menus.add.allowedGroups") ?>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- List groups -->
                                    <div class="col-md-6 mt-2 d-none" id="listAllowedGroups">
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
                                <div class="d-flex flex-wrap justify-content-between mt-3">
                                    <div class="buttons">
                                        <button type="submit" class="btn btn-primary">
                                            <?= LangManager::translate("core.btn.save") ?>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- List menus -->

        <div class="container col-12 mt-4">
            <div id="nested" class="card">
                <div id="menus" class="list-group col nested-sortable">
                    <?php foreach ($menus as $menu): ?>
                        <div class="list-group-item nested-1">
                            <i class="fas fa-arrows-alt handle"></i>
                            <input type="hidden" value="<?= $menu->getId() ?>" name="id[]" hidden>
                            <p class="content-editable" contenteditable="true"><?= $menu->getName() ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script async>
    /* Prevent enter on content editable*/
    document.querySelectorAll('.content-editable').forEach(item => {
        item.addEventListener('keypress', (evt) => {
            if (evt.key === 'Enter') {
                evt.preventDefault();
            }
        })
    })
</script>