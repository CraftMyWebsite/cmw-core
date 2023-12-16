<?php /* @var UserEntity $userAdmin */

/* @var CoreController $coreAdmin */

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\MenusController;
use CMW\Controller\Core\PackageController;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Core\ISideBarElements;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Model\Users\UsersModel;

$currentUser = UsersModel::getCurrentUser();

$sideBarImplementations = Loader::loadImplementations(ISideBarElements::class);

?>
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header text-center">
            <div class="logo">
                <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/"><img
                        src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Logo/logo_compact.png"
                        alt="<?= LangManager::translate('core.alt.logo') ?>">
                </a>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">

                <?php foreach ($sideBarImplementations as $package):
                    $package->beforeWidgets();
                endforeach; ?>

                <li class="sidebar-title"><?= LangManager::translate('core.general') ?></li>
                <li class="sidebar-item <?= MenusController::getInstance()->isActiveNavbarItem('dashboard') ? 'active' : '' ?>">
                    <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/dashboard"
                       class="sidebar-link">
                        <i class="fa-solid fa-table-columns"></i>
                        <span><?= LangManager::translate("core.dashboard.title") ?></span>
                    </a>
                </li>
                <?php
                foreach (PackageController::getCorePackages() as $package):
                    foreach ($package->menus() as $menu):
                        if ($menu->getLang() === EnvManager::getInstance()->getValue('LOCALE')):
                            if (is_null($menu->getUrl())):?>
                                <li class="sidebar-item has-sub <?= MenusController::getInstance()->isActiveNavbar($menu->getSubMenus()) ? 'active' : '' ?>">
                                    <a href="#" class="sidebar-link">
                                        <i class="<?= $menu->getIcon() ?>"></i>
                                        <span><?= $menu->getTitle() ?></span>
                                    </a>
                                    <ul class="submenu <?= MenusController::getInstance()->isActiveNavbar($menu->getSubMenus()) ? 'active' : '' ?>">
                                        <?php foreach ($menu->getSubMenus() as $submenu):

                                            if (UsersModel::hasPermission($currentUser, $submenu->getPermission())):?>
                                                <li class="submenu-item <?= MenusController::getInstance()->isActiveNavbarItem($submenu->getUrl()) ? 'active' : '' ?>">
                                                    <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/<?= $submenu->getUrl() ?>">
                                                        <?= $submenu->getTitle() ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php else:
                                if (UsersModel::hasPermission($currentUser, $menu->getPermission())):?>
                                    <li class="sidebar-item <?= MenusController::getInstance()->isActiveNavbarItem($menu->getUrl()) ? 'active' : '' ?>">
                                        <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/<?= $menu->getUrl() ?>"
                                           class="sidebar-link">
                                            <i class="<?= $menu->getIcon() ?>"></i>
                                            <span><?= $menu->getTitle() ?></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                <li class="sidebar-title"><?= LangManager::translate('core.packages') ?></li>

                <?php foreach (PackageController::getInstalledPackages() as $package):
                    foreach ($package->menus() as $menu):
                        if ($menu->getLang() === EnvManager::getInstance()->getValue('LOCALE')):
                            if (is_null($menu->getUrl())):?>
                                <li class="sidebar-item has-sub <?= MenusController::getInstance()->isActiveNavbar($menu->getSubMenus()) ? 'active' : '' ?>">
                                    <a href="#" class="sidebar-link">
                                        <i class="<?= $menu->getIcon() ?>"></i>
                                        <span><?= $menu->getTitle() ?></span>
                                    </a>
                                    <ul class="submenu <?= MenusController::getInstance()->isActiveNavbar($menu->getSubMenus()) ? 'active' : '' ?>">
                                        <?php foreach ($menu->getSubMenus() as $submenu):

                                            if (UsersModel::hasPermission($currentUser, $submenu->getPermission())):?>
                                                <li class="submenu-item <?= MenusController::getInstance()->isActiveNavbarItem($submenu->getUrl()) ? 'active' : '' ?>">
                                                    <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/<?= $submenu->getUrl() ?>">
                                                        <?= $submenu->getTitle() ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </li>
                            <?php else:
                                if (UsersModel::hasPermission($currentUser, $menu->getPermission())):?>
                                    <li class="sidebar-item <?= MenusController::getInstance()->isActiveNavbarItem($menu->getUrl()) ? 'active' : '' ?>">
                                        <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/<?= $menu->getUrl() ?>"
                                           class="sidebar-link">
                                            <i class="<?= $menu->getIcon() ?>"></i>
                                            <span><?= $menu->getTitle() ?></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                <?php foreach ($sideBarImplementations as $package):
                    $package->afterWidgets();
                endforeach; ?>

            </ul>
        </div>
    </div>
</div>