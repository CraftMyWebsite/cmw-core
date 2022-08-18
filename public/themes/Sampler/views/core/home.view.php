<?php use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\UsersModel;

$title = "Accueil";
$description = "page d'accueil de CraftMyWebsite"; ?>


<h1>Accueil</h1>

<main>
    <?php if (UsersController::isAdminLogged()) : ?>
        <a href="./cmw-admin/">Accès rapide à l'administration</a>
    <?php else : ?>
        <a href="./login">Se connecter</a>
    <?php endif; ?>
    <p>
        Bienvenue sur votre nouveau site
        <?= LangManager::translate("core.eat.pasta", lineBreak: true) ?>
        <?= LangManager::translate("core.eat.potatoes", ["name" => UsersModel::getCurrentUser()->getUsername()]) ?>
    </p>

</main>

