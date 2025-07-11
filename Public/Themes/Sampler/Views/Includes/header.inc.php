<?php

use CMW\Controller\Core\PackageController;
use CMW\Controller\Shop\Admin\Item\ShopItemsController;
use CMW\Controller\Shop\Admin\Payment\ShopPaymentsController;
use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Core\MenusModel;
use CMW\Utils\Website;

$menus = MenusModel::getInstance();
?>

<nav class="bg-gray-900 border-gray-700">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img data-cmw-attr="src:header:site_image" data-cmw-style="width:header:site_image_width" alt="CMW Logo" />
            <span class="self-center text-2xl font-semibold whitespace-nowrap text-white"><?= Website::getWebsiteName() ?></span>
        </a>
        <button data-collapse-toggle="navbar-multi-level" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-multi-level" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
            </svg>
        </button>
        <div class="hidden w-full lg:block lg:w-auto" id="navbar-multi-level">
            <ul class="flex flex-col font-medium p-4 lg:p-0 mt-4 border rounded-lg  lg:space-x-8 rtl:space-x-reverse lg:flex-row lg:mt-0 lg:border-0 ">
                <?php foreach ($menus->getMenus() as $menu): ?>
                    <?php if ($menu->isUserAllowed()): ?>
                        <li id="multiLevelDropdownButton" data-dropdown-toggle="dropdown-<?= $menu->getId() ?>"
                            class="cursor-pointer block py-2 pr-4 pl-3
                            <?php if ($menu->urlIsActive()) { echo 'text-blue-500'; } ?>
                             rounded hover:bg-gray-100 lg:hover:bg-transparent lg:hover:text-blue-700 lg:p-0">
                            <a href="<?= $menu->getUrl() ?>" <?= !$menu->isTargetBlank() ?: "target='_blank'" ?>
                            ><?= $menu->getName() ?></a>
                        </li>
                        <div id="dropdown-<?= $menu->getId() ?>" class="hidden z-10 bg-white divide-y divide-gray-100 shadow-lg w-56">
                            <?php foreach ($menus->getSubMenusByMenu($menu->getId()) as $subMenu): ?>
                                <?php if ($subMenu->isUserAllowed()): ?>
                                    <ul class="py-1 text-gray-700" aria-labelledby="multiLevelDropdownButton">
                                        <li>
                                            <a href="<?= $subMenu->getUrl() ?>" id="doubleDropdownButton" data-dropdown-toggle="doubleDropdown-<?= $subMenu->getId() ?>" data-dropdown-placement="right-start" type="button" class="flex justify-between items-center py-2 px-4 w-full hover:bg-gray-100" <?= !$subMenu->isTargetBlank() ?: "target='_blank'" ?>><?= $subMenu->getName() ?></a>
                                            <?php foreach ($menus->getSubMenusByMenu($subMenu->getId()) as $subSubMenu): ?>
                                                <?php if ($subSubMenu->isUserAllowed()): ?>
                                                    <div id="doubleDropdown-<?= $subMenu->getId() ?>" class="hidden z-10 bg-white divide-y divide-gray-100 shadow-lg w-56" style="position: absolute; inset: 0px auto auto 0px; margin: 0px; transform: translate(10px, 300px);" data-popper-reference-hidden="" data-popper-escaped="" data-popper-placement="right-start">
                                                        <ul class="py-1 text-gray-700" aria-labelledby="doubleDropdownButton">
                                                            <li>
                                                                <a href="<?= $subSubMenu->getUrl() ?>" class="block py-2 px-4 hover:bg-gray-100" <?= !$subSubMenu->isTargetBlank() ?: "target='_blank'" ?>><?= $subSubMenu->getName() ?></a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </li>
                                    </ul>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php if (UsersController::isUserLogged()): ?>
            <ul class="flex flex-col bg-gray-800 rounded-lg border border-gray-100 md:flex-row md:space-x-8 md:mt-0 md:text-sm md:font-medium md:border-0">
                <li id="multiLevelDropdownButton" data-dropdown-toggle="dropdown1"
                    class="cursor-pointer hover:bg-gray-700 font-medium rounded-lg text-sm px-5 py-2.5">
                    <i class="mr-2 fa-solid fa-user"></i><?= UsersSessionsController::getInstance()->getCurrentUser()->getPseudo() ?></li>
                <div id="dropdown1" class="hidden z-10 w-44 bg-gray-900 rounded divide-y divide-gray-100 shadow">
                    <ul class="py-1 text-sm text-white" aria-labelledby="multiLevelDropdownButton">
                        <?php if (UsersController::isAdminLogged()): ?>
                            <li>
                                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin"
                                   target="_blank" class="block py-2 px-4 hover:bg-gray-800"><i
                                        class="fa-solid fa-screwdriver-wrench"></i> Administration</a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile"
                               class="block py-2 px-4 hover:bg-gray-800"><i class="fa-regular fa-address-card"></i>
                                Profil</a>
                        </li>
                        <?php if (PackageController::isInstalled('Shop')): ?>
                            <li>
                                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>shop/settings"
                                   class="block py-2 px-4 hover:bg-gray-800"><i class="fa-solid fa-gear"></i>
                                    Paramètres</a>
                            </li>
                            <li>
                                <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>shop/history"
                                   class="block py-2 px-4 hover:bg-gray-800"><i class="fa-solid fa-clipboard-list"></i>
                                    Commandes</a>
                            </li>
                            <?php if (PackageController::isInstalled('Shopextendedtoken')): ?>
                                <li>
                                    <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>shop/tokens"
                                       class="block py-2 px-4 hover:bg-gray-800"><?= ShopPaymentsController::getInstance()->getPaymentByVarName('extendedToken')->faIcon() ?>
                                        <?= ShopItemsController::getInstance()->getPriceTypeMethodsByVarName('extendedToken')->name() ?></a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    <div class="py-1">
                        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>logout"
                           class="block py-2 px-4 text-sm text-red-700 hover:bg-gray-800"><i
                                class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
                    </div>
                </div>
            </ul>
        <?php else: ?>
                    <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login"
                       class="bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 md:px-5 md:py-2.5 mr-1 md:mr-2">Connexion</a>
        <?php endif; ?>
    </div>
</nav>

