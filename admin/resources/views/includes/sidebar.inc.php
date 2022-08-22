<?php /* @var \CMW\Entity\Users\UserEntity $userAdmin */

/* @var \CMW\Controller\CoreController $coreAdmin */

use CMW\Manager\Lang\LangManager; ?>
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
                <img src="<?= getenv("PATH_SUBFOLDER") ?>admin/resources/images/identity/logo_compact.png"
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

                //Get the package folders
                $packagesFolder = 'app/package/';
                $scannedDirectory = array_diff(scandir($packagesFolder), array('..', '.'));
                foreach ($scannedDirectory as $package) :
                    $strJsonFileContents = file_get_contents("app/package/$package/infos.json");
                    try {
                        $packageInfos = json_decode($strJsonFileContents, true, 512, JSON_THROW_ON_ERROR);
                    } catch (JsonException $e) {
                    }


                    foreach ($packageInfos['menus'] as $packageMenus):

                        $nameMenu = $packageMenus['name_menu_' . getenv("LOCALE")] ?? $packageMenus['name_menu'];

                        if (isset($packageMenus["urls_submenu"])) :
                            $urlsSubMenu = $packageMenus["urls_submenu_" . getenv("LOCALE")] ?? $packageMenus["urls_submenu"]; ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon <?= $packageMenus['icon_menu'] ?>"></i>
                                    <p><?= $nameMenu ?><i class="right fas fa-angle-left"></i></p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <?php foreach ($urlsSubMenu as $subMenuName => $subMenuUrl) : ?>
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
                                <a href="<?= getenv("PATH_SUBFOLDER") ?>cmw-admin/<?= $packageMenus['url_menu'] ?>"
                                   class="nav-link">
                                    <i class="nav-icon <?= $packageMenus['icon_menu'] ?>"></i>
                                    <p><?= $nameMenu ?></p>
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