<?php

return [
    'title' => 'CraftMyWebsite | Installation',
    'desc' => 'Installation du CMS CraftMyWebsite',
    'welcome' => [
        'title' => 'Bienvenue',
        'subtitle' => "Merci d'avoir choisi CraftMyWebsite pour votre site web !",
        'config' => [
            'title' => 'Faisons le point sur votre configuration',
        ],
        "content" => "<p>Si votre configuration n'est pas suffisante merci de vous référer aux <a class='text-primary' href='https://craftmywebsite.fr/docs/fr/users/commencer/prerequis' target='_blank'>prérequis</a>
                      avant de continuer l'installation.</p>
                      <p>En cas de demande de support auprès de CraftMyWebsite ces informations peuvent nous être utiles pour déterminer
                      l'environnement dans lequel vous êtes. Merci de bien vouloir noter les informations que vous ne connaissez pas.</p>
                      <p>Passons maintenant à l'installation de votre nouveau site ...</p>",
        "readaccept" => "J'ai lu et j'accepte les conditions générales d'utilisations",
        "cgu" => "Conditions Générales d'Utilisation",
        "error" => [
            "cgu" => "Merci d'accepter les CGU avant de procéder à l'installation de votre site CraftMyWebsite"
        ],
        'folder_check_fix_it' => 'Veuillez corriger les problèmes d\'accès pour garantir le bon fonctionnement de CraftMyWebsite !',
        'folder_check_button' => 'Verifier les droits d\'accès',
        'folder_check_no_access' => 'Non accessible en écriture',
        'folder_check_not_found' => 'Non trouvé',
    ],
    "bundle" => [
        "custom" => "Personnalisé",
        "includes" => "Ce bundle inclut les packages :",
        "customText" => "<p>Personnalisez votre installation vous-même.</p><p>Ceci ne veut pas dire qu'il n'est pas possible de le personnaliser avec d'autres bundle.</p>",
    ],
    'password' => [
        'strenght' => 'Force du mot de passe :',
        'notmatch' => 'Les mots de passe ne correspondent pas !'
    ],
    "search" => "Rechercher",
    "config" => [
        "title" => "Configuration",
        "db" => [
            "db" => "Base de données",
            "name" => "Nom",
            "login" => "Identifiant",
            "name_about" => "Généralement <code>localhost</code>. Si localhost ne fonctionne pas, veuillez vous renseigner auprès de votre hébergeur.",
            "port" => "Port",
            "address" => "Adresse",
            "pass" => "Mot de passe"
        ],
        "settings" => [
            "settings" => "Réglages",
            "devmode" => "Activer le mode développeur",
            "devmode_about" => "ATTENTION ! À n'utiliser qu'en connaissance de cause.<br>L'activer inutilement peut exposer votre site à des vulnérabilités. Il est fortement déconseillé de l'activer pour un site en production.",
            "site_folder" => "Dossier d'installation",
            "site_folder_about" => "Généralement <code>/</code>. Si CraftMyWebsite se trouve dans un dossier, veuillez indiquer <code>/dossier/</code>."
        ]
    ],
    'details' => [
        'title' => 'Détails',
        'website' => [
            'name' => 'Nom du site',
            'description' => 'Description',
            'description_placeholder' => 'Découvrez mon nouveau site grâce à CraftMyWebsite'
        ]
    ],
    "packages" => [
        "title" => "Choix des packages",
        "sub_title" => "Cliquez pour sélectionner",
        "list_title" => "Liste des packages",
        "free" => "Gratuit",
        "version" => "Version",
        "demo" => "Démo",
        "search" => "Rechercher",
        "tags" => "Tags",
        "help" => [
            "title" => "Personnalisez votre installation",
            "content" => "<b>Informations :</b> Cette étape vous permet en quelques clics d'installer les packages dont vous aurez
                besoin pour bien commencer votre site.<br>
                Cette configuration n'est pas définitive, il est possible d'en ajouter ou d'en retirer par la suite via votre panel d'administration.<br><br>
                <b>Presets : </b>Les presets sélectionnent pour vous les packages les mieux adaptés à vos besoins, tout en vous offrant la possibilité d'ajouter ou de retirer des options selon vos exigences.<br>",
            "footer" => "**Survolez un package pour en savoir plus"
        ]
    ],
    'themes' => [
        'title' => "Choix d'un thème",
        'sub_title' => "Cliquez sur l'image pour sélectionner",
        'compatibility' => 'Compatibilité',
        'more' => "Plus d'informations"
    ],
    'administrator' => [
        'title' => 'Compte Administrateur',
    ],
    'finish' => [
        'title' => 'Félicitations !',
        'desc' => 'Votre site est maintenant prêt !',
        'review' => 'Revoyons ensemble votre configuration :',
        'version' => 'Version CMW :',
        'Theme' => 'Thème :',
        'bundle' => 'Bundle :',
        'package' => 'Packages :',
        'goToMySite' => 'Aller sur mon site',
    ],
    'steps' => [
        0 => 'Bienvenue',
        1 => 'Configuration',
        2 => 'Détails',
        3 => 'Bundles',
        4 => 'Packages',
        5 => 'Thèmes',
        6 => 'Administrateur',
        7 => 'Terminé',
    ],
];
