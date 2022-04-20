<?php $title = "Accueil";
$description = "page d'accueil de CraftMyWebsite"; ?>

<?php ob_start(); ?>

<h1>Accueil</h1>

<main>
    <a href="./cmw-admin/">Accès rapide à l'administration</a>
</main>

<?php $content = ob_get_clean(); ?>