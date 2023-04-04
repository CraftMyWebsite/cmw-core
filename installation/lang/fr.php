<?php

return [
    "title" => "CraftMyWebsite | Installation",
    "desc" => "Installation du CMS CraftMyWebsite",
    "welcome" => [
        "title" => "Bienvenue",
        "subtitle" => "Merci d'avoir choisi CraftMyWebsite pour votre site web !",
        "config" => [
            "title" => "Faisons le point sur votre configuration",
        ],
        "content" => "<p>Si votre configuration n'est pas bonne merci de vous referer aux <a class='text-primary' href='' target='_blank'>prérequis</a>
                      avant de continuer l'installation.</p>
                      <p>En cas de demande de support auprès de CraftMyWebsite ces informations peuvent nous être utiles pour determiner
                      l'environnement dans lequel vous êtes. Merci de bien vouloir noter les informations que vous ne connaissez pas.</p>
                      <p>Passons maintenant à l'installation de votre nouveau site ...</p>"
    ],

    "config" => [
        "title" => "Configuration",
        "db" => [
            "db" => "Base de données",
            "name" => "Nom",
            "login" => "Identifiant",
            "name_about" => "Généralement <code>localhost</code>. Si localhost ne fonctionne pas, veuillez demander l'information à votre hébergeur.",
            "port" => "Port",
            "address" => "Addresse",
            "pass" => "Mot de passe"
        ],
        "settings" => [
            "settings" => "Réglages",
            "devmode" => "Activer le mode développeur",
            "devmode_about" => "ATTENTION ! A n'utiliser qu'en connaissance de cause.<br>Le cocher inutilement peut engendrer des failles sur votre site internet. Il est fortement déconseillé d'activer cette option pour un site en production",
            "site_folder" => "Dossier d'installation",
            "site_folder_about" => "Généralement <code>/</code>. Si CraftMyWebsite se trouve dans un dossier, veuillez indiquer <code>/dossier/</code>."
        ]
    ],

    "steps" => [
        0 => "Bienvenue",
        1 => "Configuration",
        2 => "Détails",
        3 => "Bundles",
        4 => "Packages",
        5 => "Thèmes",
        6 => "Administrateur",
        7 => "Terminé",
    ],
];