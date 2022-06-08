<?php $title = "Accueil";
$description = "page d'accueil de CraftMyWebsite"; ?>

<?php ob_start(); ?>

<h1>Accueil</h1>

<main>
    <a href="./cmw-admin/">Accès rapide à l'administration</a>

    <p>
        Bienvenue sur le site de <b><?= GLOBAL_GET_NAME ?></b>
    </p>

</main>

<?php $content = ob_get_clean(); ?>