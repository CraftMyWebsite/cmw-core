<?php /* @var UserEntity $userAdmin */

/* @var CoreController $coreAdmin */

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\PackageController;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Lang\LangManager;

?>
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header text-center">
            <div class="logo">
                <a href="<?= getenv("PATH_SUBFOLDER") ?>cmw-admin/"><img
                            src="<?= getenv("PATH_SUBFOLDER") ?>admin/resources/assets/images/logo/logo_compact.png"
                            alt="Logo de CMW" srcset=""/></a>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Général</li>
                <li class="sidebar-item active">
                    <a href="<?= getenv("PATH_SUBFOLDER") ?>cmw-admin/dashboard" class="sidebar-link">
                        <i class="fa-solid fa-table-columns"></i>
                        <span><?= LangManager::translate("core.dashboard.title") ?></span>
                    </a>
                </li>
                <?php
                foreach (PackageController::getInstalledPackages() as $package):
                    foreach ($package->getMenus() as $menu):
                        if (!empty($menu->getSubmenu())): ?>

                            <li class="sidebar-item has-sub">
                                <a href="#" class="sidebar-link">
                                    <i class="<?= $menu->getIcon() ?>"></i>
                                    <span><?= $menu->getName() ?></span>
                                </a>
                                <ul class="submenu">
                                    <?php foreach ($menu->getSubmenu() as $subMenuName => $subMenuUrl): ?>
                                        <li class="submenu-item">
                                            <a href="<?= getenv("PATH_SUBFOLDER") ?>cmw-admin/<?= $subMenuUrl ?>">
                                                <?= $subMenuName ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else : ?>
                            <li class="sidebar-item">
                                <a href="<?= getenv("PATH_SUBFOLDER") ?>cmw-admin/<?= $menu->getUrl() ?>"
                                   class="sidebar-link">
                                    <i class="<?= $menu->getIcon() ?>"></i>
                                    <span><?= $menu->getName() ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <li class="sidebar-title">Thèmes</li>
                <li class="sidebar-title">Packages officiel</li>
                <li class="sidebar-title">Packages</li>
            </ul>
        </div>
    </div>
</div>