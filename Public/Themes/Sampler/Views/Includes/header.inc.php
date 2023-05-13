<?php

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Utils\Website;

?>
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-light fixed-top py-3 bg-dark" id="mainNav">
    <div class="container px-4 px-lg-5">
        <div class="navbar-brand"><?= Website::getName() ?></div>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
                aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto my-2 my-lg-0">
                <?php if (UsersController::isAdminLogged()) : ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin">
                            Accès à l'administration
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login">Connexion</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>