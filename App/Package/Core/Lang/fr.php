<?php

return [
    "home" => "Accueil",
    "cgu" => "CGU",
    "cgv" => "CGV",
    "nolink" => "Pas de lien ou de rediréction",
    "general" => "Général",
    "package" => "Package",
    "packages" => "Packages",
    "themes" => "Themes",
    "alt" => [
        "logo" => "Logo CMW",
    ],
    "header" => [
        "notification" => "Notifications",
        "cms_ver" => "CMS : Version",
        "cms_update" => "Une mises à jours du CMS est disponible !",
        "update_to" => "Veuillez mettre à jours vers",
        "package" => "Package",
        "theme" => "Thème",
        "all_is_fine" => "Tout va bien",
        "is_up" => "Votre site est entièrement à jour",
    ],
    "condition" => [
        "title" => "Condition general",
        "cgv" => "Condition général de vente (CGV)",
        "cgu" => "Condition général d'utilisation (CGU)",
        "activecgv" => "Activer les CGV",
        "activecgu" => "Activer les CGU",
        "content" => "Contenue :",
        "updateby" => "Mis à jour par",
        "on" => "le",
    ],
    "dashboard" => [
        "title" => "Tableau de bord",
        "desc" => "Bienvenue sur votre panneau d'administration !",
        "total_member" => "Membres totaux",
        "best_views" => "Record de visites",
        "numbers_views" => "Nombres de visites et d'inscriptions",
        "daily_visits" => "Visites du jours",
        "monthly_visits" => "Visites du mois",
        "total_visits" => "Visistes totales",
        "welcome" => "Bienvenue",
        "site_info" => "Information du site",
        "name" => "Nom :",
        "description" => "Description :",
        "edit" => "Modifier ces informations",
        "visits" => "Visites",
        "registers" => "Inscriptions",
        "days" => "Jours",
        "weeks" => "Semaines",
        "months" => "Mois",
    ],
    "menus" => [
        "title" => "Menus",
        "desc" => "Gérez les menus de votre site",
        "delete_title" => "Supression de :",
        "delete_message" => "Cette suppression est définitive",
        "send_to" => "Renvoie vers :",
        "add_sub_menu" => "Ajout d'un sous-menu dans",
        "add" => [
            "name" => "Nom du menu",
            "name_hint" => "Votes",
            "targetBlank" => "Ouvrir la page dans un nouvel onglet",
            "choice" => "Type de lien",
            "package" => "Package",
            "package_select" => "Sélection du package",
            "custom" => "Personnalisé",
            "custom_hint" => "https://store.monsite.fr",
            "allowedGroups" => "Autorisez certains rôles à voir ce menu",
            "group_select" => "Sélection des rôles",
            "toaster" => [
                "success" => "Menu ajouté avec succès !",
            ],
        ],
    ],
    "editor" => [
        "title" => "Configuration Editor",
        "desc" => "Personnaliser l'éditeur de pages",
        "style" => "Style du syntax highlighter",
        "preview" => 'Vous pouvez prévisualiser le rendu des styles <a href="https://highlightjs.org/static/demo/" target="_blank">ici</a>',
    ],
    "config" => [
        "head" => "Réglages",
        "title" => "Configuration",
        "desc" => "Configurez votre site CMW !",
        "favicon" => "Modifiez le favicon de votre site",
        "favicon_tips" => 'CraftMyWebsite accepte uniquement les images <a href="https://www.icoconverter.com" target="_blank">.ico</a> pour des soucis de performances.',
        "dateFormat" => "Format des dates",
        "dateFormatTooltip" => "Vous pouvez personalisé le façon dont vous affichez les dates",
        "custom" => "-- Personnalisez-moi --",
    ],
    "Lang" => [
        "title" => "Langues",
        "desc" => "Configurez les langues de votre site CMW !",
        "change" => "Changez la langue du site",
    ],
    "website" => [
        "name" => "Nom de votre site",
        "description" => "Description de votre site",
    ],
    "minecraft" => [
        "ip" => "Addresse IP de votre serveur",
        "register" => "Autorisez uniquement les comptes Minecraft PREMIUM à s'inscrire sur votre site",
    ],
    "database" => [
        "error" => "Erreur base de donnée: ",
    ],
    "toaster" => [
        "success" => "Succès",
        "warning" => "Attention",
        "error" => "Erreur",
        "internalError" => "Erreur interne",
        "config" => [
            "success" => "Configuration modifiée avec succès !",
        ],
        "mail" => [
            "test" => "Mail envoyé à l'adresse %mail%"
        ],
        "Theme" => [
            "regenerate" => "Configuration du thème regénérée"
        ],
        "db" => [
            "config" => [
                "success" => "Configuration fonctionnelle",
                "error" => "Configuration invalide",
                "alreadyInstalled" => "Base de donnée déjà installée !"
            ],
            "missing_inputs" => "Merci de remplir tous les champs !",
        ],
        "security" => [
            "healthReport" => [
                "delete" => "Health Reports supprimés"
            ],
        ],
    ],
    "datatables" => [
        "list" => [
            "processing" => "Traitement en cours...",
            "search" => "Rechercher&nbsp; ",
            "lenghtmenu" => "Afficher _MENU_ &eacute;l&eacute;ments",
            "setlimit" => "Afficher {select} &eacute;lement par page",
            "info" => "Affichage de l\'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
            "info_vanilla" => "Affichage des &eacute;lements {start} &agrave; {end} sur {rows} &eacute;l&eacute;ments",
            "info_empty" => "Affichage de l\'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
            "info_filtered" => "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
            "info_postfix" => "",
            "loadingrecords" => "Chargement en cours...",
            "zerorecords" => "Aucun &eacute;l&eacute;ment &agrave; afficher",
            "emptytable" => "Aucune donnée disponible dans le tableau",
            "first" => "Premier",
            "previous" => "Pr&eacute;c&eacute;dent",
            "next" => "Suivant",
            "last" => "Dernier",
            "sort" => [
                "ascending" => ": activer pour trier la colonne par ordre croissant",
                "descending" => ": activer pour trier la colonne par ordre décroissant",
            ],
        ],
    ],
    "Theme" => [
        "myThemes" => "Mes thèmes",
        "market" => "Market",
        "details" => "Details",
        "install" => "Installer",
        "notVerified" => "Non verifié par CMW.",
        "active" => "Thème actif",
        "configure" => "Configurer",
        "reset" => "Réinitialiser",
        "reinstall" => "Réinstaller",
        "description" => "Déscription : ",
        "update" => "Mises à jour disponible !",
        "descriptionManualInstall" => "Ce thème est installé manuellement, il n'est pas enregistrer auprès de CraftMyWebsite.<br>Utilisez ce thème en conaissance de cause.<br>Si vous développez ce thème pour le publier ensuite sur le Market de CraftMyWebsite ne tenez pas compte de ce message.",
        "descriptionIsSampler" => "Sampler est le thème par défaut fournis avec CraftMyWebsite.",
        "author" => "Autheur : ",
        "demo" => "Démo",
        "activate" => "Activer",
        "downloads" => "Téléchargements : ",
        "themeVersion" => "Version du thème : ",
        "CMWVersion" => "Version CMW recommandée : ",
        "close" => "Fermer",
        "appearance" => "Apparence de ",
        "verification" => "Vérification",
        "verificationText" => "Attention, ceci va réinitialiser tout les paramètres par defaut de votre thème, êtes vous sûr de vouloir continuer ?",
        "config" => [
            "title" => "Gestion",
            "description" => "Gérez les thèmes de votre site",
            "select" => "Choisissez votre thème",
            "list" => [
                "title" => "Liste des Thèmes officiels",
                "info" => "Vous pouvez directement télécharger nos Thèmes sur notre Market",
                "name" => "Nom",
                "version" => "Version",
                "cmw_version" => "Version CMW",
                "downloads" => "Nombre de téléchargement",
                "download" => "Télécharger",
            ],
            "regen_config" => "Re-générer la config du thème"
        ],
        "manage" => [
            "title" => "Gérez votre thème <b>%Theme%</b>",
            "description" => "Gérez votre thème pour le personnaliser à votre guise !"
        ],
        "toasters" => [
            "update" => [
                "success" => "Thème %theme% mis à jour avec succès"
            ],
        ],
    ],
    "Package" => [
        "title" => "Packages",
        "my_packages" => "Mes packages",
        "delete" => "Supprimer",
        "install" => "Installer",
        "update" => "Mettre à jour",
        "demo" => "Démo",
        "close" => "Fermer",
        "removeTitle" => "Voulez-vous supprimer",
        "removeText" => "La suppression de ce package est définitive.<br>Voulez-vous continuer ?",
        "description" => "Déscription: ",
        "descriptionNotAvailable" => "Les déscription ne sont pas disponnible pour les packages local",
        "details" => "Détails",
        "author" => "Autheur : ",
        "downloads" => "Téléchargements: ",
        "version" => "Version du package: ",
        "versionCMW" => "Version CMW recommandée: ",
        "notVerified" => "Non verifié par CMW.",
        "updateAvailable" => "Mises à jour disponible !",
        "desc" => "Gérer vos packages",
        "myPackages" => "Mes packages",
        "market" => "Market",
        "toasters" => [
            "install" => [
                "success" => "Package %package% installé avec succès !"
            ],
            "delete" => [
                "success" => "Package %package% supprimé avec succès",
                "error" => "Impossible de supprimer le package %package%"
            ],
            "update" => [
                "success" => "Package %package% mis à jour avec succès !"
            ],
        ],
        "versionDistant" => "Dernière version en ligne"
    ],
    "mail" => [
        "config" => [
            "title" => "Configuration mails",
            "description" => "Gérez les mails de votre site",
            "enableSMTP" => "Activation des mails SMTP",
            "senderMail" => "Adresse mail d'envoie",
            "replyMail" => "Adresse mail de réponse",
            "serverSMTP" => "Adresse du serveur SMTP",
            "userSMTP" => "Utilisateur SMTP",
            "passwordSMTP" => "Mot de passe",
            "portSMTP" => "Port SMTP",
            "protocol" => "Protocol d'envoie",
            "footer" => "Footer de vos mails",
            "formatting" => "Mise en forme",
            "name" => "Nom d'affichage :",
            "placeholder" => "Nom de votre site",
            "test" => [
                "btn" => "Testez votre configuration",
                "title" => "Essaye dès maintenant ta configuration",
                "warning" => "Pensez à bien sauvegarder votre configuration avant de débuter le test !",
                "description" => "Vous pouvez tester votre configuration en envoyant un mail à votre adresse e-mail.",
                "receiverMail" => "Adresse e-mail du destinataire",
                "receiverMailPlaceholder" => "Entrez l'adresse e-mail",
            ]
        ]
    ],
    "downloads" => [
        "errors" => [
            "internalError" => "Erreur interne avec la resource %name% - %version%"
        ]
    ],
    "updates" => [
        "title" => "Mettez à jour votre site",
        "description" => "Mettez à jour votre site CraftMyWebsite",
        "pageTitle" => "Mises à jours du CMS",
        "updateButton" => "Mettre à jour",
        "warningUpdate" => "Attention ! Vous n'utilisez pas la dernière version du CMS, veuillez le mettre à jour dès maintenant.",
        "updateTo" => "Veuillez mettre à jour vers",
        "isUp" => "Votre CMS est à jour !",
        "availableFrom" => "Disponnible depuis le :",
        "lastNote" => "Note de version :",
        "previousVersion" => "Version prècedentes",
        "publishAt" => "Publié le",
        "errors" => [
            "download" => "Impossible de télécharger la dernière version du CMS.",
            "nullFileUpdate" => "Cette version n'à pas de fichier de MAJ.",
            "prepareArchive" => "Impossible de préparer l'archive pour la MAJ.",
            "deletedFiles" => "Impossible de supprimer les anciens fichiers.",
            "deleteFile" => "Impossible de supprimer le fichier %file%",
            "sqlUpdate" => "Impossible de mettre à jour la base de données.",
        ],
        "success" => "Mise à jour de votre site réussie",
    ],
    "security" => [
        "title" => "Sécurité",
        "description" => "Gérez la sécurité de votre site",
        "no_captcha" => "Pas de captcha",
        "free_key" => "Obtenez vos clé ici gratuitement :",
        "captcha" => [
            "title" => "Gestion du captcha",
            "type" => "Type de captcha",
        ],
        "healthReport" => [
            "title" => "Health Report",
            "subtitle" => "Késako ?",
            "content" => "<p>Notre health report permet à notre équipe de support de vous aider en envoyant un maximum
                    d'informations (non sensibles) à propos de votre site.</p>

                <p>
                    <b>Pensez à supprimer vos health reports une fois le support terminé !
                        Sinon vous vous exposez votre site à des failles.</b>
                </p>

                <p>
                    <i>Il est <b>impératif</b> d'envoyer vos Health Report uniquement à <b>l'équipe CraftMyWebsite</b> si on vous le demande.</i>
                </p>",
            "emplacement" => "Emplacement du Health Report",
            "copy" => "Copier le contenu"
        ],
    ],
    "maintenance" => [
        "title" => "Maintenance",
        "description" => "Gérez les maintenances de votre site",
        "settings" => [
            "title" => "Réglages",
            "targetDateTitle" => "Fin de la maintenance",
            "loginRegister" => [
                'title' => "Connexions / Inscriptions",
                'type' => [
                    0 => 'Tout désactiver',
                    1 => 'Activer les connexions / inscription',
                    2 => 'Activer uniquement les connexions',
                ],
            ],
            "maintenanceTitle" => [
                'label' => "Titre de la maintenance",
                'placeholder' => 'Nous revenons très vite !'
            ],
            "maintenanceDescription" => [
                'label' => "Description de la maintenance",
                'placeholder' => 'Nous faisons quelques modifications, nous revenons très vite !'
            ],
            "toaster" => [
                "enabled" => "Maintenance activée",
                "disabled" => "Maintenance désactivée",
                "error" => "Impossible de mettre à jour la maintenance"
            ],
        ],
    ],
    "footer" => [
        "left" => "Copyright &copy; 2014 - " . date("Y") . " Tous droits réservés.",
        "right" => "Merci d'utiliser <a target='_blank' href='https://craftmywebsite.fr/'>CraftMyWebsite</a>.",
        "used" => "Vous utilisez la version ",
        "upgrade" => "Veuillez mettre à jours vers ",
    ],
    "btn" => [
        "save" => "Sauvegarder",
        "delete" => "Supprimer",
        "delete_forever" => "Supprimer définitivement",
        "close" => "Fermer",
        "send" => "Envoyer",
        "download" => "Télécharger",
        "add" => "Ajouter",
        "edit" => "Modifier",
        "action" => "Action",
        "confirm" => "Confirmer",
        "next" => "Suivant",
        "try" => "Tester",
        "continue" => "Continuer",
        "generate" => "Générer",
        "enable" => "Activer",
        "enabled" => "Activé",
    ],
    "months" => [
        1 => "Janvier",
        2 => "Fevrier",
        3 => "Mars",
        4 => "Avril",
        5 => "Mai",
        6 => "Juin",
        7 => "Juillet",
        8 => "Août",
        9 => "Septembre",
        10 => "Octobre",
        11 => "Novembre",
        12 => "Decembre"
    ],
    "week" => "Semaine ",
    "errors" => [
        "requests" => [
            'required' => 'Champs %key% manquant',
            'empty' => 'Champs %key% vide',
            'slug' => 'Slug %key% invalide',
            'minLength' => 'Le champs %key% doit contenir plus de %min% caractères',
            'maxLength' => 'Le champs %key% doit contenir moins de %max% caractères',
            'betweenLength' => 'Le champs %key% doit contenir entre %min% et %max% caractères',
            'dateTime' => 'Le champs %key% doit être une date valide (%format%)',
            'getValue' => 'Valeur introuvable %key%',
            'type' => 'Type invalide pour %key%'
        ],
        "editConfiguration" => "Impossible de modifier la configuration %config%"
    ]
];