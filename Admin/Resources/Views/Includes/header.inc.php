<?php

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\MenusController;
use CMW\Controller\Core\PackageController;
use CMW\Entity\Users\UserEntity;
use CMW\Interface\Core\ISideBarElements;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Notification\NotificationModel;
use CMW\Model\Users\UsersModel;

$user = UsersModel::getCurrentUser();
$currentUser = UsersModel::getCurrentUser();
$sideBarImplementations = Loader::loadImplementations(ISideBarElements::class);
$notificationNumber = NotificationModel::getInstance()->countUnreadNotification();
$notifications = NotificationModel::getInstance()->getUnreadNotification();

/* @var UserEntity $userAdmin */
/* @var CoreController $coreAdmin */

$installedPackages = PackageController::getInstalledPackages();

$hasGamePackage = false;

foreach ($installedPackages as $package) {
    if ($package->isGame()) {
        $hasGamePackage = true;
        break;  // Pas besoin de continuer à vérifier les autres paquets
    }
}
?>


<nav class="nav">
    <div class="px-3 py-[.1rem] lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                        type="button" class="sm:hidden">
                    <i class="fa-solid fa-bars fa-lg"></i>
                </button>
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/"
                   class="w-48 hidden sm:flex">
                    <img
                        src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Img/logo_dark.png"
                        class="bg-contain dark:hidden" alt="Logo"/>
                    <img
                        src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Img/logo.png"
                        class="bg-contain hidden dark:block" alt="Logo"/>
                </a>
                <div class="ml-2 sm:ml-11">
                    <div class="px-3">
                        <a href="<?= EnvManager::getInstance()->getValue('PATH_URL') ?>" target="_blank"><i
                                class="fa-solid fa-arrow-up-right-from-square"></i></a>
                    </div>
                </div>
                <div class="ml-2">
                    <?php CoreController::getInstance()->getPackagesTopBarElements(); ?>
                </div>
            </div>
            <div class="flex items-center">
                <div>
                    <button type="button" class="relative  p-2.5"  data-dropdown-toggle="dropdown-notification">
                        <i class="fa-solid fa-bell fa-lg"></i>
                        <?php if ($notificationNumber): ?>
                        <div class="absolute inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 border-2 border-white rounded-full top-0 dark:border-gray-900" style="right: -0.3rem;"><?= $notificationNumber ?></div>
                        <?php endif; ?>
                    </button>
                </div>
                <div style="width: 20rem; padding-right: 16px; max-height: 800px" class="z-50 hidden space-y-2 overflow-x-auto" id="dropdown-notification">
                    <?php
                        $max_notifications = 3;
                        $notification_count = 0;
                        foreach ($notifications as $notification):
                            if ($notification_count >= $max_notifications) {
                                break;
                            }
                            $notification_count++;
                    ?>
                        <div>
                            <div class="rounded-lg border bg-white dark:bg-gray-700 dark:border-gray-700">
                                <div class="flex justify-between p-3">
                                    <p><b><?= $notification->getPackage() ?></b><small> - <?= mb_strimwidth($notification->getTitle(), 0, 30, '...') ?></small></p>
                                    <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/notification/read/<?= $notification->getId() ?>" type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white justify-center items-center flex-shrink-0 text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#toast-message-cta" aria-label="Close">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                    </a>
                                </div>
                                <div class="border-t p-3">
                                    <p><?= mb_strimwidth($notification->getMessage(), 0, 70, '...') ?></p>
                                </div>
                                <?php if ($notification->getSlug()): ?>
                                <div class="border-t p-2 flex justify-end">
                                    <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/notification/goTo/<?= $notification->getId() ?>" class="btn-primary-sm">S'y rendre</a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php
                        endforeach;
                    ?>
                        <div class="rounded-lg border bg-white dark:bg-gray-700 dark:border-gray-700 text-center p-2">
                            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/notifications" class="link">
                                Voir toutes les notifications
                            </a>
                        </div>
                </div>
                <div>
                    <button id="theme-toggle" type="button" class="p-2.5">
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 text-gray-800" fill="currentColor"
                             viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 text-yellow-500" fill="currentColor"
                             viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
                <div class="flex items-center ml-2">
                    <div>
                        <button type="button" class="flex rounded-full" data-dropdown-toggle="dropdown-user">
                            <img class="w-8 h-8 rounded-full" src="<?= $user->getUserPicture()->getImage() ?>"
                                 alt="user">
                        </button>
                    </div>
                    <div
                        class="min-w-[10rem] z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600"
                        id="dropdown-user">
                        <div class="px-4 py-3" role="none">
                            <p class="text-sm text-gray-900 dark:text-white" role="none">
                                <?= $user->getPseudo() ?>
                            </p>
                            <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                <?= $user->getHighestRole()->getName() ?>
                            </p>
                        </div>
                        <ul class="py-1" role="none">
                            <li>
                                <a class="block px-4 py-2 text-sm text-red-400 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-gray-600"
                                   role="menuitem"
                                   href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'cmw-admin/users/manage/edit/' . $user->getId() ?>">
                                    <i class="fa-solid fa-user"></i>
                                    <?= LangManager::translate('users.users.link_profile') ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>logout"
                                   class="block px-4 py-2 text-sm text-red-400 hover:bg-gray-100 dark:text-red-400 dark:hover:bg-gray-600"
                                   role="menuitem"><i
                                        class="fa-solid fa-right-from-bracket"></i> <?= LangManager::translate('users.users.logout') ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<aside id="logo-sidebar" class="aside-nav" aria-label="Sidebar">
    <div class="h-full overflow-y-auto overflow-x-hidden">
        <?php foreach ($sideBarImplementations as $package):
            $package->beforeWidgets();
        endforeach; ?>

        <ul class="space-y-1">
            <li class="mt-4">
                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/dashboard"
                   class="a-side-nav <?= MenusController::getInstance()->isActiveNavbarItem('dashboard') ? 'side-nav-active' : '' ?>">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span class="span-side-nav"><?= LangManager::translate('core.dashboard.title') ?></span>
                </a>
            </li>
            <?php
                foreach (PackageController::getCorePackages() as $package):
                    foreach ($package->menus() as $menu):
                        if ($menu->getLang() === EnvManager::getInstance()->getValue('LOCALE')):
                            // Vérifier si le menu a des sous-menus visibles
                            $hasVisibleSubMenu = false;
                            foreach ($menu->getSubMenus() as $submenu) {
                                if (UsersModel::hasPermission($currentUser, $submenu->getPermission())) {
                                    $hasVisibleSubMenu = true;
                                    break;
                                }
                            }

                            // Si le menu n'a pas d'URL et a des sous-menus visibles
                            if (is_null($menu->getUrl()) && $hasVisibleSubMenu):
            ?>
                            <li>
                                <button type="button"
                                        class="a-side-nav <?= MenusController::getInstance()->isActiveNavbar($menu->getSubMenus()) ? 'side-nav-active" aria-expanded="true"' : '"' ?>"
                                        aria-controls="dropdown-<?= $menu->getTitle() ?>"
                                        data-collapse-toggle="dropdown-<?= $menu->getTitle() ?>">
                                    <i class="<?= $menu->getIcon() ?>"></i>
                                    <span class="span-side-nav"><?= $menu->getTitle() ?></span>
                                    <i class="fa-xs fa-solid fa-chevron-down"></i>
                                </button>
                                <ul id="dropdown-<?= $menu->getTitle() ?>"
                                    class="a-side-nav-dropdown <?= MenusController::getInstance()->isActiveNavbar($menu->getSubMenus()) ? '' : 'hidden' ?>">
                                    <?php foreach ($menu->getSubMenus() as $submenu):
                                        if (UsersModel::hasPermission($currentUser, $submenu->getPermission())): ?>
                                            <li>
                                                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/<?= $submenu->getUrl() ?>"
                                                   class="a-side-nav-drop <?= MenusController::getInstance()->isActiveNavbarItem($submenu->getUrl()) ? 'side-nav-drop-active' : '' ?>">
                                                    <?= $submenu->getTitle() ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php
                                // Si le menu a une URL et l'utilisateur a la permission
                            elseif (!is_null($menu->getUrl()) && UsersModel::hasPermission($currentUser, $menu->getPermission())):
                                        ?>
                            <li>
                                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/<?= $menu->getUrl() ?>"
                                   class="a-side-nav <?= MenusController::getInstance()->isActiveNavbarItem($menu->getUrl()) ? 'side-nav-active' : '' ?>">
                                    <i class="<?= $menu->getIcon() ?>"></i>
                                    <span class="span-side-nav"><?= $menu->getTitle() ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>

        <?php if (!empty($installedPackages)): ?>
            <div class="flex flex-no-wrap justify-center items-center py-3 px-2">
                <div class="flex-grow h-px border-b"></div>
                <div class="px-2 w-auto">
                    <p class="text-sm"><?= LangManager::translate('core.packages') ?></p>
                </div>
                <div class="flex-grow h-px border-b"></div>
            </div>
            <ul class="space-y-1">
                <?php foreach ($installedPackages as $package):
                    if (!$package->isGame()):
                        foreach ($package->menus() as $menu):
                            if ($menu->getLang() === EnvManager::getInstance()->getValue('LOCALE')):
                                // Vérifier si le menu a des sous-menus visibles
                                $hasVisibleSubMenu = false;
                                foreach ($menu->getSubMenus() as $submenu) {
                                    if (UsersModel::hasPermission($currentUser, $submenu->getPermission())) {
                                        $hasVisibleSubMenu = true;
                                        break;
                                    }
                                }

                                if (is_null($menu->getUrl()) && $hasVisibleSubMenu): ?>
                                    <li>
                                        <button type="button"
                                                class="a-side-nav <?= MenusController::getInstance()->isActiveNavbar($menu->getSubMenus()) ? 'side-nav-active" aria-expanded="true"' : '"' ?>"
                                                aria-controls="dropdown-<?= $menu->getTitle() ?>"
                                                data-collapse-toggle="dropdown-<?= $menu->getTitle() ?>">
                                            <i class="<?= $menu->getIcon() ?>"></i>
                                            <span class="span-side-nav"><?= $menu->getTitle() ?></span>
                                            <i class="fa-xs fa-solid fa-chevron-down"></i>
                                        </button>
                                        <ul id="dropdown-<?= $menu->getTitle() ?>"
                                            class="a-side-nav-dropdown <?= MenusController::getInstance()->isActiveNavbar($menu->getSubMenus()) ? '' : 'hidden' ?>">
                                            <?php foreach ($menu->getSubMenus() as $submenu):
                                                if (UsersModel::hasPermission($currentUser, $submenu->getPermission())): ?>
                                                    <li>
                                                        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/<?= $submenu->getUrl() ?>"
                                                           class="a-side-nav-drop <?= MenusController::getInstance()->isActiveNavbarItem($submenu->getUrl()) ? 'side-nav-drop-active' : '' ?>">
                                                            <?= $submenu->getTitle() ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php
                                else:
                                    if (!is_null($menu->getUrl()) && UsersModel::hasPermission($currentUser, $menu->getPermission())):
                                        ?>
                                        <li>
                                            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/<?= $menu->getUrl() ?>"
                                               class="a-side-nav <?= MenusController::getInstance()->isActiveNavbarItem($menu->getUrl()) ? 'side-nav-active' : '' ?>">
                                                <i class="<?= $menu->getIcon() ?>"></i>
                                                <span class="span-side-nav"><?= $menu->getTitle() ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($installedPackages) && $hasGamePackage): ?>
            <div class="flex flex-no-wrap justify-center items-center py-3 px-2">
                <div class="flex-grow h-px border-b"></div>
                <div class="px-2 w-auto">
                    <p class="text-sm"><?= LangManager::translate('core.games') ?></p>
                </div>
                <div class="flex-grow h-px border-b"></div>
            </div>
            <ul class="space-y-1 mb-8">
                <?php foreach ($installedPackages as $package):
                    if ($package->isGame()):
                        foreach ($package->menus() as $menu):
                            if ($menu->getLang() === EnvManager::getInstance()->getValue('LOCALE')):
                                // Vérifier si le menu a des sous-menus visibles
                                $hasVisibleSubMenu = false;
                                foreach ($menu->getSubMenus() as $submenu) {
                                    if (UsersModel::hasPermission($currentUser, $submenu->getPermission())) {
                                        $hasVisibleSubMenu = true;
                                        break;
                                    }
                                }

                                if (is_null($menu->getUrl()) && $hasVisibleSubMenu): ?>
                                    <li>
                                        <button type="button"
                                                class="a-side-nav <?= MenusController::getInstance()->isActiveNavbar($menu->getSubMenus()) ? 'side-nav-active" aria-expanded="true"' : '"' ?>"
                                                aria-controls="dropdown-<?= $menu->getTitle() ?>"
                                                data-collapse-toggle="dropdown-<?= $menu->getTitle() ?>">
                                            <i class="<?= $menu->getIcon() ?>"></i>
                                            <span class="span-side-nav"><?= $menu->getTitle() ?></span>
                                            <i class="fa-xs fa-solid fa-chevron-down"></i>
                                        </button>
                                        <ul id="dropdown-<?= $menu->getTitle() ?>"
                                            class="a-side-nav-dropdown <?= MenusController::getInstance()->isActiveNavbar($menu->getSubMenus()) ? '' : 'hidden' ?>">
                                            <?php foreach ($menu->getSubMenus() as $submenu):
                                                if (UsersModel::hasPermission($currentUser, $submenu->getPermission())): ?>
                                                    <li>
                                                        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/<?= $submenu->getUrl() ?>"
                                                           class="a-side-nav-drop <?= MenusController::getInstance()->isActiveNavbarItem($submenu->getUrl()) ? 'side-nav-drop-active' : '' ?>">
                                                            <?= $submenu->getTitle() ?>
                                                        </a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                <?php
                                else:
                                    if (!is_null($menu->getUrl()) && UsersModel::hasPermission($currentUser, $menu->getPermission())):
                                        ?>
                                        <li>
                                            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin/<?= $menu->getUrl() ?>"
                                               class="a-side-nav <?= MenusController::getInstance()->isActiveNavbarItem($menu->getUrl()) ? 'side-nav-active' : '' ?>">
                                                <i class="<?= $menu->getIcon() ?>"></i>
                                                <span class="span-side-nav"><?= $menu->getTitle() ?></span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php foreach ($sideBarImplementations as $package):
            $package->afterWidgets();
        endforeach; ?>
    </div>
</aside>

<section class="main-content">