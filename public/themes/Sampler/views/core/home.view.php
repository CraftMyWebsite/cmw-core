<?php use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Users\UsersModel;

$title = "Accueil";
$description = "page d'accueil de CraftMyWebsite"; ?>


<h1>Accueil</h1>

<main>
    <?php if (UsersController::isAdminLogged()) : ?>
        <a href="./cmw-admin/">Accès rapide à l'administration</a>
    <?php endif; ?>

    <?php if(UsersModel::getLoggedUser() === -1): ?>
        <a href="./login">Se connecter</a>
    <?php else: ?>
        <a href="./register">S'enregistrer</a>
    <?php endif; ?>
    <p>
        Bienvenue sur votre nouveau site
    </p>

</main>

