<?php

use CMW\Entity\Users\RoleEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var array $packagesLinks */
/* @var RoleEntity[] $roles */
/* @var \CMW\Model\Core\MenusModel $menus */

$title = LangManager::translate("core.menus.title");
$description = LangManager::translate("core.menus.desc");
?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fas fa-bars"></i> <span class="m-lg-auto"><?= LangManager::translate("core.menus.title") ?></span>
    </h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-body">

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
                                <input class="form-check-input allowedGroups" type="checkbox"
                                       name="allowedGroupsToggle"
                                >
                                <label class="form-check-label" for="allowedGroups">
                                    <?= LangManager::translate("core.menus.add.allowedGroups") ?>
                                </label>
                            </div>
                        </div>
                        <!-- List groups -->
                        <div class="col-md-6 mt-2 d-none listAllowedGroups">
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
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check"></i>
                            <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                        </button>
                    </div>

                </form>
            </div>


        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-body">
                <?php foreach ($menus->getMenus() as $menu): ?>


                    <div class="d-flex gap-2">
                        <div>
                            <div style="display: none" class="loader" id="loader"><i style="color: #0ab312"
                                                                                     class="fa-xs fa-solid fa-arrows-rotate fa-spin"></i>
                            </div>
                            <?php if ($menu->getOrder() !== 1): ?>
                                <div class="sorter"><a onclick="load()"
                                                       href="menus/menuDown/<?= $menu->getId() ?>/<?= $menu->getOrder() ?>"><i
                                                class="fa-xs fa-solid fa-chevron-up"></i></a></div>
                            <?php endif; ?>
                            <?php if ($menu->getOrder() !== $menu->getLastMenuOrder()): ?>
                                <div class="sorter"><a onclick="load()"
                                                       href="menus/menuUp/<?= $menu->getId() ?>/<?= $menu->getOrder() ?>"><i
                                                class="fa-xs fa-solid fa-chevron-down"></i></a></div>
                            <?php endif; ?>
                        </div>

                        <div style="width: 100%">
                            <div class="card-in-card table-responsive mb-3">
                                <table class="table-borderless table mb-1 table-hover">
                                    <thead>
                                    <tr>
                                        <th><?= $menu->getName() ?> -
                                            <?php if ($menu->getUrl() === "#"): ?>
                                                <small><?= LangManager::translate('core.nolink') ?></small>
                                            <?php else: ?>
                                                <small><?= LangManager::translate('core.menus.send_to',
                                                        ['url' => $menu->getUrl()]) ?>
                                                    <?= $menu->getUrl() ?></small>
                                            <?php endif; ?>
                                        </th>
                                        <th class="text-end">
                                            <a type="button" data-bs-toggle="modal"
                                               data-bs-target="#add-submenu-<?= $menu->getId() ?>">
                                                <i class="text-success me-3 fa-solid fa-circle-plus"></i>
                                            </a>
                                            <a type="button" data-bs-toggle="modal"
                                               data-bs-target="#delete-<?= $menu->getId() ?>">
                                                <i class="text-danger fas fa-trash-alt"></i>
                                            </a>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($menus->getSubMenusByMenu($menu->getId()) as $subMenu): ?>
                                        <tr>
                                            <td class="ps-4"><?= $subMenu->getName() ?> -
                                                <?php if ($subMenu->getUrl() === "#"): ?>
                                                    <small><?= LangManager::translate('core.nolink') ?></small>
                                                <?php else: ?>
                                                    <small><?= LangManager::translate('core.menus.send_to',
                                                            ['url' => $subMenu->getUrl()]) ?>
                                                        <?= $subMenu->getUrl() ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <span style="display: none" class="loader me-3"><i
                                                            style="color: #0ab312"
                                                            class="fa-xs fa-solid fa-arrows-rotate fa-spin"></i></span>
                                                <?php if ($subMenu->getOrder() !== 1): ?>
                                                    <span class="sorter me-3"><a onclick="load()"
                                                                                 href="menus/submenuDown/<?= $subMenu->getId() ?>/<?= $subMenu->getOrder() ?>/<?= $menu->getId() ?>"><i
                                                                    class="fa-xs fa-solid fa-chevron-up"></i></a></span>
                                                <?php endif; ?>
                                                <?php if ($subMenu->getOrder() !== $subMenu->getLastSubMenuOrder($menu->getId())): ?>
                                                    <span class="sorter me-3"><a onclick="load()"
                                                                                 href="menus/submenuUp/<?= $subMenu->getId() ?>/<?= $subMenu->getOrder() ?>/<?= $menu->getId() ?>"><i
                                                                    class="fa-xs fa-solid fa-chevron-down"></i></a></span>
                                                <?php endif; ?>
                                                <a type="button" data-bs-toggle="modal"
                                                   data-bs-target="#add-sub-submenu-<?= $subMenu->getId() ?>">
                                                    <i class="text-success me-3 fa-solid fa-circle-plus"></i>
                                                </a>
                                                <a type="button" data-bs-toggle="modal"
                                                   data-bs-target="#delete-<?= $subMenu->getId() ?>">
                                                    <i class="text-danger fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php foreach ($menus->getSubMenusByMenu($subMenu->getId()) as $subSubMenu): ?>
                                            <tr>
                                                <td class="ps-5"><?= $subSubMenu->getName() ?> -
                                                    <?php if ($subSubMenu->getUrl() === "#"): ?>
                                                        <small><?= LangManager::translate('core.nolink') ?></small>
                                                    <?php else: ?>
                                                        <small><?= LangManager::translate('core.menus.send_to',
                                                                ['url' => $subSubMenu->getUrl()]) ?>
                                                            <?= $subSubMenu->getUrl() ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end">
                                                    <span style="display: none" class="loader me-3"><i
                                                                style="color: #0ab312"
                                                                class="fa-xs fa-solid fa-arrows-rotate fa-spin"></i></span>
                                                    <?php if ($subSubMenu->getOrder() !== 1): ?>
                                                        <span class="sorter me-3"><a onclick="load()"
                                                                                     href="menus/submenuDown/<?= $subSubMenu->getId() ?>/<?= $subSubMenu->getOrder() ?>/<?= $subMenu->getId() ?>"><i
                                                                        class="fa-xs fa-solid fa-chevron-up"></i></a></span>
                                                    <?php endif; ?>
                                                    <?php if ($subSubMenu->getOrder() !== $subMenu->getLastSubMenuOrder($subMenu->getId())): ?>
                                                        <span class="sorter me-3"><a onclick="load()"
                                                                                     href="menus/submenuUp/<?= $subSubMenu->getId() ?>/<?= $subSubMenu->getOrder() ?>/<?= $subMenu->getId() ?>"><i
                                                                        class="fa-xs fa-solid fa-chevron-down"></i></a></span>
                                                    <?php endif; ?>
                                                    <a type="button" data-bs-toggle="modal"
                                                       data-bs-target="#delete-<?= $subSubMenu->getId() ?>">
                                                        <i class="text-danger fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <!--
                                           --MODAL SUPPRESSION SUB SUB MENU--
                                            -->
                                            <div class="modal fade text-left" id="delete-<?= $subSubMenu->getId() ?>"
                                                 tabindex="-1" role="dialog"
                                                 aria-labelledby="myModalLabel160" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered"
                                                     role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger">
                                                            <h5 class="modal-title white" id="myModalLabel160">
                                                                <?= LangManager::translate("core.menus.delete_title",
                                                                    ['menu' => $subSubMenu->getName()]) ?>
                                                                <?= $subSubMenu->getName() ?>
                                                            </h5>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?= LangManager::translate("core.menus.delete_message") ?>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light-secondary"
                                                                    data-bs-dismiss="modal">
                                                                <i class="bx bx-x"></i>
                                                                <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                            </button>
                                                            <a href="menus/delete/<?= $subSubMenu->getId() ?>/<?= $subSubMenu->getOrder() ?>/<?= $subMenu->getId() ?>"
                                                               class="btn btn-danger ml-1">
                                                                <i class="bx bx-check"></i>
                                                                <span class=""><?= LangManager::translate("core.btn.delete") ?></span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                        <!--
                                        --MODAL ADD SUB-SUBMENU FORUM--
                                        -->
                                        <div class="modal fade " id="add-sub-submenu-<?= $subMenu->getId() ?>"
                                             tabindex="-1" role="dialog" aria-labelledby="myModalLabel160"
                                             aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg"
                                                 role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title white"
                                                            id="myModalLabel160"><?= LangManager::translate("core.menus.add_sub_menu",
                                                                ['menu' => $subMenu->getName()]) ?>
                                                            <?= $subMenu->getName() ?>
                                                        </h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="menus/add-submenu" method="post"
                                                              name="sub-submenu">
                                                            <?php (new SecurityManager())->insertHiddenToken() ?>
                                                            <input hidden name="parentId" type="text"
                                                                   value="<?= $subMenu->getId() ?>">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-6">
                                                                    <h6><?= LangManager::translate("core.menus.add.name") ?>
                                                                        :</h6>
                                                                    <div class="form-group position-relative has-icon-left">
                                                                        <input type="text" name="name"
                                                                               class="form-control"
                                                                               placeholder="<?= LangManager::translate("core.menus.add.name_hint") ?>"
                                                                               required>
                                                                        <div class="form-control-icon">
                                                                            <i class="fa-solid fa-text-width"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 mt-3">
                                                                    <div class="form-check form-switch">
                                                                        <input class="form-check-input" type="checkbox"
                                                                               name="targetBlank"
                                                                               id="targetBlank">
                                                                        <label class="form-check-label"
                                                                               for="targetBlank"><?= LangManager::translate("core.menus.add.targetBlank") ?></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-2 align-items-center">
                                                                <div class="col-md-6">
                                                                    <h6><?= LangManager::translate("core.menus.add.choice") ?>
                                                                        :</h6>
                                                                    <div class="form-group">
                                                                        <select class="choices form-select super-choice"
                                                                                name="choice" required>
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
                                                                    <h6><?= LangManager::translate("core.menus.add.package_select") ?>
                                                                        :</h6>
                                                                    <div class="form-group">
                                                                        <select class="choices form-select"
                                                                                name="slugPackage">
                                                                            <?php foreach ($packagesLinks as $package => $routes):
                                                                                if ($routes !== []):?>
                                                                                    <option disabled>
                                                                                        ──── <?= $package ?> ────
                                                                                    </option>
                                                                                <?php endif; ?>
                                                                                <?php foreach ($routes as $name => $route): ?>
                                                                                <option value="<?= $route ?>"><?= $name ?></option>
                                                                            <?php endforeach; ?>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6 d-none addCustom">
                                                                    <h6><?= LangManager::translate("core.menus.add.custom") ?>
                                                                        :</h6>
                                                                    <div class="form-group position-relative has-icon-left">
                                                                        <input type="text" name="slugCustom"
                                                                               class="form-control"
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
                                                                        <input class="form-check-input allowedGroups"
                                                                               type="checkbox"
                                                                               name="allowedGroupsToggle"
                                                                        >
                                                                        <label class="form-check-label"
                                                                               for="allowedGroups">
                                                                            <?= LangManager::translate("core.menus.add.allowedGroups") ?>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <!-- List groups -->
                                                                <div class="col-md-6 mt-2 d-none listAllowedGroups">
                                                                    <h6><?= LangManager::translate("core.menus.add.group_select") ?>
                                                                        :</h6>
                                                                    <div class="form-group">
                                                                        <select class="choices form-select"
                                                                                name="allowedGroups[]" multiple>
                                                                            <?php foreach ($roles as $role): ?>
                                                                                <option
                                                                                        value="<?= $role->getId() ?>"><?= $role->getName() ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light-secondary"
                                                                data-bs-dismiss="modal">
                                                            <i class="bx bx-x"></i>
                                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                        </button>
                                                        <button type="submit" form="sub-submenu"
                                                                class="btn btn-primary ml-1">
                                                            <i class="bx bx-check"></i>
                                                            <span class=""><?= LangManager::translate("core.btn.add") ?></span>
                                                        </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!--
                                       --MODAL SUPPRESSION SUB MENU--
                                        -->
                                        <div class="modal fade text-left" id="delete-<?= $subMenu->getId() ?>"
                                             tabindex="-1" role="dialog"
                                             aria-labelledby="myModalLabel160" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                                 role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <h5 class="modal-title white"
                                                            id="myModalLabel160"><?= LangManager::translate("core.menus.delete_title",
                                                                ['menu' => $subMenu->getName()]) ?>
                                                            <?= $subMenu->getName() ?>
                                                        </h5>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?= LangManager::translate("core.menus.delete_message") ?>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light-secondary"
                                                                data-bs-dismiss="modal">
                                                            <i class="bx bx-x"></i>
                                                            <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                        </button>
                                                        <a href="menus/delete/<?= $subMenu->getId() ?>/<?= $subMenu->getOrder() ?>/<?= $menu->getId() ?>"
                                                           class="btn btn-danger ml-1">
                                                            <i class="bx bx-check"></i>
                                                            <span class=""><?= LangManager::translate("core.btn.delete") ?></span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!--
                    --MODAL SUBMENU FORUM--
                    -->
                    <div class="modal fade " id="add-submenu-<?= $menu->getId() ?>" tabindex="-1" role="dialog"
                         aria-labelledby="myModalLabel160" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg"
                             role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title white"
                                        id="myModalLabel160">
                                        <?= LangManager::translate("core.menus.add_sub_menu", ['menu' => $menu->getName()]) ?>
                                        <?= $menu->getName() ?>
                                    </h5>
                                </div>
                                <div class="modal-body">
                                    <form action="menus/add-submenu" method="post">
                                        <?php (new SecurityManager())->insertHiddenToken() ?>
                                        <input hidden name="parentId" type="text" value="<?= $menu->getId() ?>">
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
                                                    <select class="choices form-select super-choice" name="choice"
                                                            required>
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
                                                <h6><?= LangManager::translate("core.menus.add.package_select") ?>
                                                    :</h6>
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
                                                    <input class="form-check-input allowedGroups" type="checkbox"
                                                           name="allowedGroupsToggle"
                                                    >
                                                    <label class="form-check-label" for="allowedGroups">
                                                        <?= LangManager::translate("core.menus.add.allowedGroups") ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <!-- List groups -->
                                            <div class="col-md-6 mt-2 d-none listAllowedGroups">
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


                    <!--
                   --MODAL SUPPRESSION MENU--
                    -->
                    <div class="modal fade text-left" id="delete-<?= $menu->getId() ?>" tabindex="-1" role="dialog"
                         aria-labelledby="myModalLabel160" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title white"
                                        id="myModalLabel160"><?= LangManager::translate("core.menus.delete_title",
                                            ['menu' => $menu->getName()]) ?>
                                        <?= $menu->getName() ?>
                                    </h5>
                                </div>
                                <div class="modal-body">
                                    <?= LangManager::translate("core.menus.delete_message") ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                        <i class="bx bx-x"></i>
                                        <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                    </button>
                                    <a href="menus/delete/<?= $menu->getId() ?>/<?= $menu->getOrder() ?>"
                                       class="btn btn-danger ml-1">
                                        <i class="bx bx-check"></i>
                                        <span class=""><?= LangManager::translate("core.btn.delete") ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>


<script>
    function load() {
        let loader = document.getElementsByClassName("loader");
        for (let f = 0; f < loader.length; f++) {
            loader[f].style.display = "inline";
        }
        let sorter = document.getElementsByClassName('sorter');
        for (let i = 0; i < sorter.length; i++) {
            sorter[i].style.display = 'none';
        }
    }
</script>