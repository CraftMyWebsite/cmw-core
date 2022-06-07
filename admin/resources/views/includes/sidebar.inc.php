<?php use CMW\Model\Users\usersModel;?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?=getenv("PATH_SUBFOLDER")?>cmw-admin/" class="brand-link">
        <img src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/images/identity/logo_compact.png" alt="<?=ALT_LOGO?>" class="brand-image elevation-3">
        <span class="brand-text font-weight-light">Craft My Website</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/images/identity/logo_compact.png" class="elevation-2" alt="<?= ALT_LOGO ?>">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php $user = new usersModel; $user->fetch($_SESSION['cmwUserId']); echo $user->userPseudo; ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <li class="nav-item">
                    <a href="<?=getenv("PATH_SUBFOLDER")?>cmw-admin/dashboard" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p><?= CORE_DASHBOARD ?></p>
                    </a>
                </li>

                <?php $packagesFolder = 'app/package/';
                $scannedDirectory = array_diff(scandir($packagesFolder), array('..', '.'));
                foreach ($scannedDirectory as $package) :
                    $strJsonFileContents = file_get_contents("app/package/$package/infos.json");
                    try {
                        $packageInfos = json_decode($strJsonFileContents, true, 512, JSON_THROW_ON_ERROR);
                    } catch (JsonException $e) {
                    }

                    $nameMenu = $packageInfos['name_menu_' . getenv("LOCALE")] ?? $packageInfos['name_menu'];



                    if(isset($packageInfos["urls_submenu"])) :
                        $urlsSubMenu = $packageInfos["urls_submenu_" . getenv("LOCALE")] ?? $packageInfos["urls_submenu"]; ?>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon <?=$packageInfos['icon_menu']?>"></i>
                                <p><?=$nameMenu?><i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <?php foreach ($urlsSubMenu as $subMenuName => $subMenuUrl) : ?>
                                    <li class="nav-item">
                                        <a href="<?=getenv("PATH_SUBFOLDER")?>cmw-admin/<?=$subMenuUrl?>" class="nav-link">
                                            <p><?=$subMenuName?></p>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>

                    <?php else : ?>
                        <li class="nav-item">
                            <a href="<?=getenv("PATH_SUBFOLDER")?>cmw-admin/<?=$packageInfos['url_menu']?>" class="nav-link">
                                <i class="nav-icon <?=$packageInfos['icon_menu']?>"></i>
                                <p><?=$nameMenu?></p>
                            </a>
                        </li>
                <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>