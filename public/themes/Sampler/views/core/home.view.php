<?php $title = "Accueil";
$description = "page d'accueil de CraftMyWebsite"; ?>



<h1>Accueil</h1>

<main>
    <a href="./cmw-admin/">Accès rapide à l'administration</a>

    <p>
        Bienvenue sur votre nouveau site, <b><?= getCurrentUser()->getUsername() ?></b>
    </p>

</main>

