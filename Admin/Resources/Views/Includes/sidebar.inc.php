<?php /* @var UserEntity $userAdmin */

/* @var CoreController $coreAdmin */

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\MenusController;
use CMW\Controller\Core\PackageController;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Website;

?>
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header text-center">
            <div class="logo">
                <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/"><img
                            src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Logo/logo_compact.png"
                            alt="<?= LangManager::translate('core.alt.logo') ?>" srcset=""/></a>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title"><?= LangManager::translate('core.general') ?></li>
                <li class="sidebar-item <?= Website::isCurrentPage(EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'cmw-admin/dashboard') ? 'active' : '' ?>">
                    <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/dashboard"
                       class="sidebar-link">
                        <i class="fa-solid fa-table-columns"></i>
                        <span><?= LangManager::translate("core.dashboard.title") ?></span>
                    </a>
                </li>
                <?php foreach (PackageController::getCorePackages() as $package):
                    foreach ($package->getMenus() as $menu):
                        if (!empty($menu->getSubmenu())): ?>

                            <li class="sidebar-item has-sub <?= MenusController::getInstance()->isActiveNavbar($menu->getSubmenu()) ? 'active' : '' ?>">
                                <a href="#" class="sidebar-link">
                                    <i class="<?= $menu->getIcon() ?>"></i>
                                    <span><?= $menu->getName() ?></span>
                                </a>
                                <ul class="submenu <?= MenusController::getInstance()->isActiveNavbar($menu->getSubmenu()) ? 'active' : '' ?>">
                                    <?php foreach ($menu->getSubmenu() as $subMenuName => $subMenuUrl): ?>
                                        <li class="submenu-item <?= MenusController::getInstance()->isActiveNavbarItem($subMenuUrl) ? 'active' : '' ?>">
                                            <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/<?= $subMenuUrl ?>">
                                                <?= $subMenuName ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else : ?>
                            <li class="sidebar-item <?= MenusController::getInstance()->isActiveNavbarItem($menu->getUrl()) ? 'active' : '' ?>">
                                <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/<?= $menu->getUrl() ?>"
                                   class="sidebar-link">
                                    <i class="<?= $menu->getIcon() ?>"></i>
                                    <span><?= $menu->getName() ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>

                <li class="sidebar-title"><?= LangManager::translate('core.packages') ?></li>

                <?php foreach (PackageController::getInstalledPackages() as $package):
                    foreach ($package->getMenus() as $menu):
                        if (!empty($menu->getSubmenu())): ?>
                            <li class="sidebar-item has-sub <?= MenusController::getInstance()->isActiveNavbar($menu->getSubmenu()) ? 'active' : '' ?>">
                                <a href="#" class="sidebar-link">
                                    <i class="<?= $menu->getIcon() ?>"></i>
                                    <span><?= $menu->getName() ?></span>
                                </a>
                                <ul class="submenu <?= MenusController::getInstance()->isActiveNavbar($menu->getSubmenu()) ? 'active' : '' ?>">
                                    <?php foreach ($menu->getSubmenu() as $subMenuName => $subMenuUrl): ?>
                                        <li class="submenu-item <?= MenusController::getInstance()->isActiveNavbarItem($subMenuUrl) ? 'active' : '' ?>">
                                            <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/<?= $subMenuUrl ?>">
                                                <?= $subMenuName ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else : ?>
                            <li class="sidebar-item <?= MenusController::getInstance()->isActiveNavbarItem($menu->getUrl()) ? 'active' : '' ?>">
                                <a href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>cmw-admin/<?= $menu->getUrl() ?>"
                                   class="sidebar-link">
                                    <i class="<?= $menu->getIcon() ?>"></i>
                                    <span><?= $menu->getName() ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>

            </ul>
        </div>
    </div>
</div>