<?php /* @var UserEntity $userAdmin */

/* @var CoreController $coreAdmin */

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\PackageController;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Lang\LangManager;

?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= getenv("PATH_SUBFOLDER") ?>cmw-admin/" class="brand-link">
        <img src="<?= getenv("PATH_SUBFOLDER") ?>admin/resources/images/identity/logo_compact.png"
             alt="<?= LangManager::translate("core.alt.logo", lineBreak: true) ?>" class="brand-image elevation-3">
        <span class="brand-text font-weight-light">CMW - ADMIN</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= getenv('PATH_SUBFOLDER') ?>public/uploads/users/<?= $userAdmin->getUserPicture()->getImageName() ?>"
                     class="elevation-2" alt="<?= LangManager::translate("core.alt.logo", lineBreak: true) ?>">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= $userAdmin->getUsername() ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="<?= getenv("PATH_SUBFOLDER") ?>cmw-admin/dashboard" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p><?= LangManager::translate("core.dashboard.title", lineBreak: true) ?></p>
                    </a>
                </li>

                <?php
                foreach (PackageController::getInstalledPackages() as $package):
                    foreach ($package->getMenus() as $menu):
                        if (!empty($menu->getSubmenu())): ?>

                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon <?= $menu->getIcon() ?>"></i>
                                    <p><?= $menu->getName() ?><i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php foreach ($menu->getSubmenu() as $subMenuName => $subMenuUrl): ?>
                                        <li class="nav-item">
                                            <a href="<?= getenv("PATH_SUBFOLDER") ?>cmw-admin/<?= $subMenuUrl ?>"
                                               class="nav-link">
                                                <p><?= $subMenuName ?></p>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>

                        <?php else : ?>
                            <li class="nav-item">
                                <a href="<?= getenv("PATH_SUBFOLDER") ?>cmw-admin/<?= $menu->getUrl() ?>"
                                   class="nav-link">
                                    <i class="nav-icon <?= $menu->getIcon() ?>"></i>
                                    <p><?= $menu->getName() ?></p>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>