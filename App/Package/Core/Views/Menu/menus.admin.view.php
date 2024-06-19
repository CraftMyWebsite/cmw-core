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

<div class="grid-2">
    <div class="card">
        <form action="" method="post" class="space-y-3">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <div>
                <label for="name"><?= LangManager::translate("core.menus.add.name") ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-text-width"></i>
                    <input type="text" id="name" name="name"
                           placeholder="<?= LangManager::translate("core.menus.add.name_hint") ?>"
                           required>
                </div>
            </div>
            <div>
                <label for="super-choice"><?= LangManager::translate("core.menus.add.choice") ?> :</label>
                <select id="super-choice" name="choice" class="choices" required>
                    <option value="package">
                        <?= LangManager::translate("core.menus.add.package") ?>
                    </option>
                    <option value="custom">
                        <?= LangManager::translate("core.menus.add.custom") ?>
                    </option>
                </select>
            </div>
            <div id="addPackage">
                <label for="slugPackage"><?= LangManager::translate("core.menus.add.package_select") ?> :</label>
                <select id="slugPackage" class="choices" name="slugPackage">
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
            <div id="addCustom" style="display: none">
                <label for="slugCustom"><?= LangManager::translate("core.menus.add.custom") ?> :</label>
                <div class="input-group">
                    <i class="fa-solid fa-link"></i>
                    <input type="text" id="slugCustom" name="slugCustom" class="form-control"
                           placeholder="<?= LangManager::translate("core.menus.add.custom_hint") ?>">
                </div>
            </div>
            <div>
                <label class="toggle">
                    <p class="toggle-label"><?= LangManager::translate("core.menus.add.targetBlank") ?></p>
                    <input type="checkbox" class="toggle-input" name="targetBlank" id="targetBlank">
                    <div class="toggle-slider"></div>
                </label>
            </div>
            <div>
                <label class="toggle">
                    <p class="toggle-label"><?= LangManager::translate("core.menus.add.allowedGroups") ?> :</p>
                    <input type="checkbox" class="toggle-input" name="allowedGroupsToggle" id="allowedGroups">
                    <div class="toggle-slider"></div>
                </label>
            </div>
            <div id="listAllowedGroups" style="display: none">
                <label for="allowedGroups"></label>
                <select class="choices form-select" id="allowedGroups" name="allowedGroups[]" multiple>
                    <?php foreach ($roles as $role): ?>
                        <option
                            value="<?= $role->getId() ?>"><?= $role->getName() ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-primary btn-center"><?= LangManager::translate("core.btn.add") ?></button>
        </form>
    </div>



    <div class="card space-y-2">
        <?php foreach ($menus->getMenus() as $menu): ?>


            <div class="flex gap-2 border-gray-100 dark:border-gray-700 border rounded-lg py-1 px-2">
                <div>
                    <div style="display: none" class="menu-loader" id="menu-loader">
                        <i style="color: #0ab312" class="fa-xs fa-solid fa-arrows-rotate fa-spin"></i>
                    </div>
                    <?php if ($menu->getOrder() !== 1): ?>
                        <div class="sorter"><a onclick="load()" href="menus/menuDown/<?= $menu->getId() ?>/<?= $menu->getOrder() ?>"><i class="fa-xs fa-solid fa-chevron-up"></i></a></div>
                    <?php endif; ?>
                    <?php if ($menu->getOrder() !== $menu->getLastMenuOrder()): ?>
                        <div class="sorter"><a onclick="load()" href="menus/menuUp/<?= $menu->getId() ?>/<?= $menu->getOrder() ?>"><i class="fa-xs fa-solid fa-chevron-down"></i></a></div>
                    <?php endif; ?>
                </div>

                <div style="width: 100%">

                        <table class="w-full">
                            <thead>
                            <tr class="flex justify-between hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                                <th><?= $menu->getName() ?> -
                                    <?php if ($menu->getUrl() === "#"): ?>
                                        <small><?= LangManager::translate('core.nolink') ?></small>
                                    <?php else: ?>
                                        <small><?= LangManager::translate('core.menus.send_to', ['url' => $menu->getUrl()]) ?>
                                        </small>
                                    <?php endif; ?>
                                    <?php if ($menu->isRestricted()): ?><small style="color: #af1a1a">
                                        <button data-tooltip-target="tooltip-<?= $menu->getId() ?>" data-tooltip-placement="top">Restreint <i class="fa-sharp fa-solid fa-circle-info"></i></button>
                                        <div id="tooltip-<?= $menu->getId() ?>" role="tooltip" class="tooltip-content">
                                            <?php foreach ($menus->getAllowedRoles($menu->getId()) as $allowedRoles): ?> - <?= $allowedRoles->getName() ?> <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </th>
                                <th class="space-x-2">
                                    <a style="color: #0ab312" href="menus/add-submenu/<?= $menu->getId() ?>">
                                        <i class="fa-solid fa-circle-plus"></i>
                                    </a>
                                    <a style="color: #1C64F2" href="menus/edit/<?= $menu->getId() ?>">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button style="color: #f3182b" data-modal-toggle="delete-<?= $menu->getId() ?>" type="button"><i class="fas fa-trash-alt"></i></button>
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php foreach ($menus->getSubMenusByMenu($menu->getId()) as $subMenu): ?>
                                <tr class="flex justify-between hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                                    <td class="pl-3"><?= $subMenu->getName() ?> -
                                        <?php if ($subMenu->getUrl() === "#"): ?>
                                            <small><?= LangManager::translate('core.nolink') ?></small>
                                        <?php else: ?>
                                            <small><?= LangManager::translate('core.menus.send_to',
                                                    ['url' => $subMenu->getUrl()]) ?></small>
                                        <?php endif; ?>
                                        <?php if (!$subMenu->isUserAllowed()): ?><small style="color: #af1a1a">*Accès
                                            restreint</small><?php endif; ?>
                                    </td>
                                    <td class="space-x-2">
                                                <span style="display: none" class="menu-loader me-3"><i
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
                                        <a style="color: #0ab312" href="menus/add-submenu/<?= $subMenu->getId() ?>">
                                            <i class="fa-solid fa-circle-plus"></i>
                                        </a>
                                        <a style="color: #1C64F2" href="menus/edit/<?= $subMenu->getId() ?>">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button style="color: #f3182b" data-modal-toggle="delete-<?= $subMenu->getId() ?>" type="button"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>


                                <?php foreach ($menus->getSubMenusByMenu($subMenu->getId()) as $subSubMenu): ?>
                                    <tr class="flex justify-between hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                                        <td class="pl-10"><?= $subSubMenu->getName() ?> -
                                            <?php if ($subSubMenu->getUrl() === "#"): ?>
                                                <small><?= LangManager::translate('core.nolink') ?></small>
                                            <?php else: ?>
                                                <small><?= LangManager::translate('core.menus.send_to',
                                                        ['url' => $subSubMenu->getUrl()]) ?>
                                                </small>
                                            <?php endif; ?>
                                            <?php if (!$subSubMenu->isUserAllowed()): ?><small
                                                style="color: #af1a1a">*Accès restreint</small><?php endif; ?>
                                        </td>
                                        <td class="space-x-2">
                                                    <span style="display: none" class="menu-loader me-3"><i
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
                                            <a style="color: #1C64F2" href="menus/edit/<?= $subSubMenu->getId() ?>">
                                                <i class="text-primary me-3 fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button style="color: #f3182b" data-modal-toggle="delete-<?= $subSubMenu->getId() ?>" type="button"><i class="text-danger fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                    <!--
                                   --MODAL SUPPRESSION SUB SUB MENU--
                                    -->
                                    <div id="delete-<?= $subSubMenu->getId() ?>" class="modal-container">
                                        <div class="modal">
                                            <div class="modal-header-danger">
                                                <h6><?= LangManager::translate("core.menus.delete_title", ['menu' => $subSubMenu->getName()]) ?></h6>
                                                <button type="button" data-modal-hide="delete-<?= $subSubMenu->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                            </div>
                                            <div class="modal-body">
                                                <?= LangManager::translate("core.menus.delete_message") ?>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="menus/delete/<?= $subSubMenu->getId() ?>/<?= $subSubMenu->getOrder() ?>/<?= $subMenu->getId() ?>" class="btn-danger"><?= LangManager::translate("core.btn.delete") ?></a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <!--
                               --MODAL SUPPRESSION SUB MENU--
                                -->
                                <div id="delete-<?= $subMenu->getId() ?>" class="modal-container">
                                    <div class="modal">
                                        <div class="modal-header-danger">
                                            <h6><?= LangManager::translate("core.menus.delete_title", ['menu' => $subMenu->getName()]) ?></h6>
                                            <button type="button" data-modal-hide="delete-<?= $subMenu->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                                        </div>
                                        <div class="modal-body">
                                            <?= LangManager::translate("core.menus.delete_message") ?>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="menus/delete/<?= $subMenu->getId() ?>/<?= $subMenu->getOrder() ?>/<?= $menu->getId() ?>" class="btn-danger"><?= LangManager::translate("core.btn.delete") ?></a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                </div>
            </div>


            <!--
           --MODAL SUPPRESSION MENU--
            -->
            <div id="delete-<?= $menu->getId() ?>" class="modal-container">
                <div class="modal">
                    <div class="modal-header-danger">
                        <h6><?= LangManager::translate("core.menus.delete_title", ['menu' => $menu->getName()]) ?></h6>
                        <button type="button" data-modal-hide="delete-<?= $menu->getId() ?>"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <div class="modal-body">
                        <?= LangManager::translate("core.menus.delete_message") ?>
                    </div>
                    <div class="modal-footer">
                        <a href="menus/delete/<?= $menu->getId() ?>/<?= $menu->getOrder() ?>" class="btn-danger"><?= LangManager::translate("core.btn.delete") ?></a>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    </div>

</div>

