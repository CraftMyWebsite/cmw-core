<?php use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Model\Core\ThemeModel;

$title = "Accueil";
$description = "page d'accueil de CraftMyWebsite"; ?>


<!-- Masthead-->
<header class="masthead"
        style="background: linear-gradient(to bottom, rgba(92, 77, 66, 0.8) 0%, rgba(92, 77, 66, 0.8) 100%),
            url('<?= ThemeModel::fetchImageLink("background") ?>');">
    <div class="container px-4 px-lg-5 h-100">
        <div class="row gx-4 gx-lg-5 h-100 align-items-center justify-content-center text-center">
            <div class="col-lg-8 align-self-end">
                <h1 class="text-white font-weight-bold">CraftMyWebsite</h1>
                <hr class="divider"/>
            </div>
            <div class="col-lg-8 align-self-baseline">
                <p class="text-white-75">Bienvenue sur votre site !</p>
                <p class="text-white-75 mb-5">Il est maintenant temps de commencer la configuration, connectez-vous pour
                    accéder à votre panel d'administration.</p>
                <?php if (UsersController::isAdminLogged()) : ?>
                    <a class="btn btn-primary btn-xl" target="_blank"
                       href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>cmw-admin">Panel
                        d'administration</a>
                <?php else: ?>
                    <a class="btn btn-xl" style="background: <?= ThemeModel::fetchConfigValue('buttonColor') ?>"
                       href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>login">Connexion</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
