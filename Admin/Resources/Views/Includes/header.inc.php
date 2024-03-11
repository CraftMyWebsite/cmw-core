<?php

use CMW\Controller\Core\CoreController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Theme\ThemeManager;
use CMW\Model\Users\UsersModel;

//$packagesList = PublicAPI::getData("resources/getResources&resource_type=1");
//$themesList = PublicAPI::getData("resources/getResources&resource_type=0");
$currentTheme = ThemeManager::getInstance()->getCurrentTheme()->name();
$notificationNumber = 0;

$user = UsersModel::getCurrentUser();
?>
<div id="main" class="layout-navbar">

    <nav class="navbar navbar-expand navbar-light navbar-top">
        <div class="container-fluid">
            <a href="#" class="burger-btn d-block">
                <i style="font-size: 1.5rem" class="fa-solid fa-bars"></i>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a href="<?= EnvManager::getInstance()->getValue('PATH_URL') ?>" target="_blank" class="ms-4">
                <i style="font-size: 1.5rem" class="fa-solid fa-up-right-from-square"></i>
            </a>

            <?php CoreController::getInstance()->getPackagesTopBarElements(); ?>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="dropdown navbar-nav ms-auto mb-lg-0">

                    <div class="theme-toggle d-flex gap-2 align-items-center me-4">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             aria-hidden="true" role="img" class="iconify iconify--system-uicons" width="20" height="20"
                             preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                            <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                               stroke-linejoin="round">
                                <path
                                    d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                                    opacity=".3"></path>
                                <g transform="translate(-210 -1)">
                                    <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                                    <circle cx="220.5" cy="11.5" r="4"></circle>
                                    <path
                                        d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path>
                                </g>
                            </g>
                        </svg>
                        <div class="form-check form-switch fs-6">
                            <input class="form-check-input me-0" type="checkbox" id="toggle-dark"
                                   style="cursor: pointer"/>
                            <label class="form-check-label"></label>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg"
                             aria-hidden="true" role="img" class="iconify iconify--mdi" width="20" height="20"
                             preserveAspectRatio="xMidYMid meet" viewBox="0 0 24 24">
                            <path fill="currentColor"
                                  d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z"></path>
                        </svg>
                    </div>

                    <ul class="navbar-nav ms-auto mb-lg-0">
                        <li class="nav-item dropdown me-3">
                            <!--                            <a class="nav-link active  text-gray-600" href="#" data-bs-toggle="dropdown"-->
                            <!--                               data-bs-display="static" aria-expanded="false">-->
                            <!--                                <div><i class="fa-solid fa-bell fa-xl"></i></div>-->
                            <!--                            </a>-->
                            <!--                            <ul class="dropdown-menu dropdown-menu-end notification-dropdown"-->
                            <!--                                aria-labelledby="dropdownMenuButton">-->
                            <!--                                <li class="dropdown-header">-->
                            <!--                                    <h6>-->
                            <?php //= LangManager::translate("core.header.notification") ?><!--</h6>-->
                            <!--                                </li>-->
                            <!--                                --><?php //if (UpdatesManager::checkNewUpdateAvailable()): ?>
                            <!--                                    <li class="dropdown-item notification-item">-->
                            <!--                                        <a class="d-flex align-items-center"-->
                            <!--                                           href="-->
                            <?php //= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?><!--cmw-admin/updates/cms">-->
                            <!--                                            <div class="d-flex align-items-center justify-content-center">-->
                            <!--                                                <i class="fa-solid fa-cubes text-warning fa-lg"></i>-->
                            <!--                                            </div>-->
                            <!--                                            <div class="notification-text ms-4">-->
                            <!--                                                <p class="notification-title font-bold">-->
                            <?php //= LangManager::translate("core.header.cms_ver") ?><!--</p>-->
                            <!--                                                <p class="notification-subtitle font-thin text-sm">-->
                            <?php //= LangManager::translate("core.header.cms_update") ?><!--</p>-->
                            <!--                                            </div>-->
                            <!--                                        </a>-->
                            <!--                                    </li>-->
                            <!--                                    --><?php //$notificationNumber++; endif; ?>
                            <!--                                --><?php //foreach ($packagesList as $packages): ?>
                            <!--                                    --><?php //if (PackageController::isInstalled($packages['name'])): ?>
                            <!--                                        --><?php //$localPackage = PackageController::getPackage($packages['name']); ?>
                            <!--                                        --><?php //if ($localPackage->getVersion() !== $packages['version_name']): ?>
                            <!--                                            <li class="dropdown-item notification-item">-->
                            <!--                                                <a class="d-flex align-items-center"-->
                            <!--                                                   href="-->
                            <?php //= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?><!--cmw-admin/packages/my_package">-->
                            <!--                                                    <div class="d-flex align-items-center justify-content-center">-->
                            <!--                                                        <i class="fa-solid fa-puzzle-piece text-warning fa-lg"></i>-->
                            <!--                                                    </div>-->
                            <!--                                                    <div class="notification-text ms-4">-->
                            <!--                                                        <p class="notification-title font-bold">--><?php //= LangManager::translate("core.header.package") ?>
                            <!--                                                            : -->
                            <?php //= $packages['name'] ?><!--</p>-->
                            <!--                                                        <p class="notification-subtitle font-thin text-sm">-->
                            <?php //= LangManager::translate("core.header.update_to") ?><!-- -->
                            <?php //= $packages['version_name'] ?><!--</p>-->
                            <!--                                                    </div>-->
                            <!--                                                </a>-->
                            <!--                                            </li>-->
                            <!--                                            --><?php //$notificationNumber++; endif; ?>
                            <!--                                    --><?php //endif; ?>
                            <!--                                --><?php //endforeach; ?>
                            <!--                                --><?php //foreach ($themesList as $theme): ?>
                            <!--                                    --><?php //if ($theme['name'] === $currentTheme): ?>
                            <!--                                        --><?php //$localTheme = ThemeController::getTheme($theme['name']); ?>
                            <!--                                        --><?php //if ($localTheme->getVersion() !== $theme['version_name']): ?>
                            <!--                                            <li class="dropdown-item notification-item">-->
                            <!--                                                <a class="d-flex align-items-center"-->
                            <!--                                                   href="-->
                            <?php //= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?><!--cmw-admin/packages/my_package">-->
                            <!--                                                    <div class="d-flex align-items-center justify-content-center">-->
                            <!--                                                        <i class="fa-solid fa-feather text-warning fa-lg"></i>-->
                            <!--                                                    </div>-->
                            <!--                                                    <div class="notification-text ms-4">-->
                            <!--                                                        <p class="notification-title font-bold">--><?php //= LangManager::translate("core.header.theme") ?>
                            <!--                                                            : -->
                            <?php //= $theme['name'] ?><!--</p>-->
                            <!--                                                        <p class="notification-subtitle font-thin text-sm">-->
                            <?php //= LangManager::translate("core.header.update_to") ?><!-- -->
                            <?php //= $theme['version_name'] ?><!--</p>-->
                            <!--                                                    </div>-->
                            <!--                                                </a>-->
                            <!--                                            </li>-->
                            <!--                                            --><?php //$notificationNumber++; endif; ?>
                            <!--                                    --><?php //endif; ?>
                            <!--                                --><?php //endforeach; ?>
                            <!--                                --><?php //if ($notificationNumber === 0): ?>
                            <!--                                    <li class="dropdown-item notification-item">-->
                            <!--                                        <a class="d-flex align-items-center"-->
                            <!--                                           href="#">-->
                            <!--                                            <div class="d-flex align-items-center justify-content-center">-->
                            <!--                                                <i class="fa-regular fa-thumbs-up text-success fa-lg"></i>-->
                            <!--                                            </div>-->
                            <!--                                            <div class="notification-text ms-4">-->
                            <!--                                                <p class="notification-title font-bold">-->
                            <?php //= LangManager::translate("core.header.all_is_fine") ?><!--</p>-->
                            <!--                                                <p class="notification-subtitle font-thin text-sm">-->
                            <?php //= LangManager::translate("core.header.is_up") ?><!--</p>-->
                            <!--                                            </div>-->
                            <!--                                        </a>-->
                            <!--                                    </li>-->
                            <!--                                --><?php //endif; ?>
                            <!--                            </ul>-->
                            <!--                            --><?php //if ($notificationNumber !== 0): ?>
                            <!--                                <span class="position-absolute bg-danger text-white rounded"-->
                            <!--                                      style="font-size: small; top: -0.4rem; left: 1.3rem; padding: 0 4px 0 4px">-->
                            <?php //= $notificationNumber ?><!--</span>-->
                            <!--                            --><?php //else: ?>
                            <!--                                <span class="position-absolute bg-success text-white rounded"-->
                            <!--                                      style="font-size: small; top: -0.4rem; left: 1.3rem; padding: 0 4px 0 4px">0</span>-->
                            <!--                            --><?php //endif; ?>
                        </li>
                    </ul>
                    <div class="dropdown">
                        <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-menu d-flex">
                                <div class="user-name text-end me-3">
                                    <h6 class="mb-0"><?= $user->getPseudo() ?></h6>
                                    <p class="mb-0 text-sm text-gray-600"><?= $user->getHighestRole()->getName() ?></p>
                                </div>
                                <div class="user-img d-none d-lg-flex align-items-center">
                                    <div class="avatar avatar-md">
                                        <img src="<?= $user->getUserPicture()->getImage() ?>"
                                             alt="<?= LangManager::translate("users.users.image.image_alt", ['username' => $user->getPseudo()]) ?>">
                                    </div>
                                </div>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton"
                            style="min-width: 11rem">

                            <li>
                                <a class="dropdown-item"
                                   href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'cmw-admin/users/manage/edit/' . $user->getId() ?>">
                                    <i class="fa-solid fa-user"></i>
                                    <?= LangManager::translate("users.users.link_profile") ?>
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger"
                                   href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>logout">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span><?= LangManager::translate("users.users.logout") ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>


                </div>
            </div>
        </div>
    </nav>

    <div id="main-content">
        <div class="page-heading">