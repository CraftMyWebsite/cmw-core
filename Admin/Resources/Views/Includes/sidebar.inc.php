<?php /* @var UserEntity $userAdmin */

/* @var CoreController $coreAdmin */

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\PackageController;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;

?>
<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header text-center">
            <div class="logo">
                <a href="<?= Utils::getEnv()->getValue("PATH_SUBFOLDER") ?>cmw-admin/"><img
                            src="<?= Utils::getEnv()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Logo/logo_compact.png"
                            alt="<?= LangManager::translate('core.alt.logo') ?>" srcset=""/></a>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title"><?= LangManager::translate('core.general') ?></li>
                <li class="sidebar-item <?= Utils::isCurrentPageActive(Utils::getEnv()->getValue('PATH_SUBFOLDER') . 'cmw-admin/dashboard') ? 'active' : '' ?>">
                    <a href="<?= Utils::getEnv()->getValue("PATH_SUBFOLDER") ?>cmw-admin/dashboard"
                       class="sidebar-link">
                        <i class="fa-solid fa-table-columns"></i>
                        <span><?= LangManager::translate("core.dashboard.title") ?></span>
                    </a>
                </li>
                <?php foreach (PackageController::getCorePackages() as $package):
                    foreach ($package->getMenus() as $menu):
                        if (!empty($menu->getSubmenu())):
                            $currentSlug = str_replace(Utils::getEnv()->getValue('PATH_SUBFOLDER') . 'cmw-admin/', '', $_SERVER['REQUEST_URI']); ?>

                            <li class="sidebar-item has-sub <?= in_array($currentSlug, $menu->getSubmenu(), true) ? 'active' : '' ?>">
                                <a href="#" class="sidebar-link">
                                    <i class="<?= $menu->getIcon() ?>"></i>
                                    <span><?= $menu->getName() ?></span>
                                </a>
                                <ul class="submenu <?= in_array($currentSlug, $menu->getSubmenu(), true) ? 'active' : '' ?>">
                                    <?php foreach ($menu->getSubmenu() as $subMenuName => $subMenuUrl): ?>
                                        <li class="submenu-item <?= Utils::isCurrentPageActive(Utils::getEnv()->getValue('PATH_SUBFOLDER') . 'cmw-admin/' . $subMenuUrl) ? 'active' : '' ?>">
                                            <a href="<?= Utils::getEnv()->getValue("PATH_SUBFOLDER") ?>cmw-admin/<?= $subMenuUrl ?>">
                                                <?= $subMenuName ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else : ?>
                            <li class="sidebar-item <?= Utils::isCurrentPageActive(Utils::getEnv()->getValue('PATH_SUBFOLDER') . 'cmw-admin/' . $menu->getUrl()) ? 'active' : '' ?>">
                                <a href="<?= Utils::getEnv()->getValue("PATH_SUBFOLDER") ?>cmw-admin/<?= $menu->getUrl() ?>"
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
                        if (!empty($menu->getSubmenu())):
                            $currentSlug = str_replace(Utils::getEnv()->getValue('PATH_SUBFOLDER') . 'cmw-admin/', '', $_SERVER['REQUEST_URI']); ?>

                            <li class="sidebar-item has-sub <?= in_array($currentSlug, $menu->getSubmenu(), true) ? 'active' : '' ?>">
                                <a href="#" class="sidebar-link">
                                    <i class="<?= $menu->getIcon() ?>"></i>
                                    <span><?= $menu->getName() ?></span>
                                </a>
                                <ul class="submenu <?= in_array($currentSlug, $menu->getSubmenu(), true) ? 'active' : '' ?>">
                                    <?php foreach ($menu->getSubmenu() as $subMenuName => $subMenuUrl): ?>
                                        <li class="submenu-item <?= Utils::isCurrentPageActive(Utils::getEnv()->getValue('PATH_SUBFOLDER') . 'cmw-admin/' . $subMenuUrl) ? 'active' : '' ?>">
                                            <a href="<?= Utils::getEnv()->getValue("PATH_SUBFOLDER") ?>cmw-admin/<?= $subMenuUrl ?>">
                                                <?= $subMenuName ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else : ?>
                            <li class="sidebar-item <?= Utils::isCurrentPageActive(Utils::getEnv()->getValue('PATH_SUBFOLDER') . 'cmw-admin/' . $menu->getUrl()) ? 'active' : '' ?>">
                                <a href="<?= Utils::getEnv()->getValue("PATH_SUBFOLDER") ?>cmw-admin/<?= $menu->getUrl() ?>"
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