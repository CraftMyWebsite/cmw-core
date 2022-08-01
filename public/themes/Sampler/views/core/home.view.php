<?php use CMW\Controller\Users\UsersController;

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
    </p>

</main>

