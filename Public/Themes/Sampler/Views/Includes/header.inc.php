<?php

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Core\MenusModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Website;

$menus = MenusModel::getInstance();
?>


<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-light fixed-top py-3 bg-dark" id="mainNav">
    <div class="container px-4 px-lg-5">
        <a style="text-decoration: none" href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>"><div class="navbar-brand"><?= Website::getName() ?></div></a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>

        <div class="collapse navbar-collapse" id="navbarResponsive">

            <ul class="navbar-nav ms-auto" style="">
                <?php foreach ($menus->getMenus() as $menu): ?>
                    <?php if ($menu->isUserAllowed()): ?>
                        <li class="nav-item">
                            <a href="<?= $menu->getUrl() ?>"
                               class="nav-link" <?= !$menu->isTargetBlank() ?: "target='_blank'" ?>><?= $menu->getName() ?></a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

            <ul class="navbar-nav ms-auto my-2 my-lg-0">
                <?php if (UsersController::isUserLogged()): ?>
                    <li class="drop nav-item">
                        <a href="#" class="nav-link"><?= UsersModel::getCurrentUser()->getPseudo() ?></a>
                        <div class="drop-content">
                            <a class="nav-link" style="color: #0b0b0b; padding: 5px" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>profile">Profile</a>
                            <?php if (UsersController::isAdminLogged()) : ?>
                            <a class="nav-link" style="color: #0b0b0b; padding: 5px" target="_blank" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin">Administration</a>
                            <?php endif; ?>
                            <a class="nav-link" style="color: #f00d20; padding: 5px " href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>logout">Deconnexion</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login">Connexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>register">Inscription</a>
                    </li>
                <?php endif; ?>



            </ul>
        </div>
    </div>
</nav>