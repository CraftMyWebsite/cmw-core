<?php
    /* @var \CMW\Controller\Installer\installerController $install */
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a class="brand-link">
        <img src="admin/resources/images/identity/logo_compact.png" alt="<?= CORE_ALT_LOGO ?>"
             class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-light">Craft My Website</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <div class="row mb-3 justify-content-center">
                <div class="col-3">
                    <a href="installer/lang/fr"><img src="admin/resources/vendors/flag-icon-css/flags/fr.svg"
                                              class="flag-icon" alt="Passer le site en Français"></a>
                </div>
                <div class="col-3">
                    <a href="installer/lang/en"><img src="admin/resources/vendors/flag-icon-css/flags/gb.svg"
                                              class="flag-icon" alt="Switch the site to English"></a>
                </div>
            </div>
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <?php for($i = 0; $i < 5; $i++): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $install->setActiveOnStep($i) ?>">
                            <i class="nav-icon fas fa-<?= $install->setCheckOnStep($i)?>"></i>
                            <p><?= INSTALL_STEP . " " . $i+1 ?></p>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>