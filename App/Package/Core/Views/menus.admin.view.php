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
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fas fa-bars"></i> <span class="m-lg-auto">Menus</span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-body">
                <?php foreach ($menus as $menu): ?>
                    <div class="card-in-card table-responsive mb-4">
                        <table class="table-borderless table table-hover mt-1">
                            <thead>
                            <tr>
                                <th><?= $menu->getName() ?> - <small>Renvoie vers : <?= $menu->getUrl() ?></small></th>
                                <th class="text-end">
                                    <!--<a type="button" data-bs-toggle="modal"
                                       data-bs-target="#add-forum-<?= $menu->getId() ?>">
                                        <i class="text-success me-3 fa-solid fa-circle-plus"></i>
                                    </a>-->
                                    <a type="button" data-bs-toggle="modal"
                                       data-bs-target="#delete-<?= $menu->getId() ?>">
                                        <i class="text-danger fas fa-trash-alt"></i>
                                    </a>
                                </th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                    <!--
                   --MODAL SUPPRESSION MENU--
                    -->
                    <div class="modal fade text-left" id="delete-<?= $menu->getId() ?>" tabindex="-1" role="dialog"
                         aria-labelledby="myModalLabel160" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title white" id="myModalLabel160">Supression de
                                        : <?= $menu->getName() ?></h5>
                                </div>
                                <div class="modal-body">
                                    Cette supression est définitive
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                        <i class="bx bx-x"></i>
                                        <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                    </button>
                                    <a href="menus/delete/<?= $menu->getId() ?>" class="btn btn-danger ml-1">
                                        <i class="bx bx-check"></i>
                                        <span class=""><?= LangManager::translate("core.btn.delete") ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>

                <div class="divider">
                    <a type="button" data-bs-toggle="modal" data-bs-target="#add-menu-<?= $menu->getId() ?>">
                        <div class="divider-text"><i class="fa-solid fa-circle-plus"></i> Ajouter un lien</div>
                    </a>
                </div>

                <!--
                                	--MODAL AJOUT FORUM--
                                -->
                <div class="modal fade " id="add-menu-<?= $menu->getId() ?>" tabindex="-1" role="dialog"
                     aria-labelledby="myModalLabel160" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-primary">
                                <h5 class="modal-title white" id="myModalLabel160">Ajout d'un lien dans le menu</h5>
                            </div>
                            <div class="modal-body">
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
                                    <div class="row align-items-center">
                                        <div class="col-md-6 mt-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                       name="allowedGroupsToggle"
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
                                </form>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                    <i class="bx bx-x"></i>
                                    <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                </button>
                                <button type="submit" class="btn btn-primary ml-1">
                                    <i class="bx bx-check"></i>
                                    <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                                </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>


<!-- List menus -->
<!--
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

<script async>
    /* Prevent enter on content editable*/
    document.querySelectorAll('.content-editable').forEach(item => {
        item.addEventListener('keypress', (evt) => {
            if (evt.key === 'Enter') {
                evt.preventDefault();
            }
        })
    })
</script>-->