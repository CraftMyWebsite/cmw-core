<?php

return [
    'profile' => 'Profil',
    'login' => [
        'title' => 'Connexion',
        'desc' => "Connectez-vous pour accéder au panneau d'administration",
        'remember' => 'Se souvenir de moi',
        'signin' => 'Connexion',
        'lost_password' => 'Mot de passe oublié ?',
        'register' => "S'inscrire",
        'forgot_password' => [
            'title' => 'Mot de passe oublié',
            'desc' => 'Récupérez votre mot de passe',
            'btn' => 'Confirmation',
            'mail' => [
                'object_pass' => 'Votre nouveau mot de passe %site_name%',
                'object_link' => 'Changer votre mot de passe sur %site_name%',
                'body' => 'Voici votre nouveau mot de passe à changer rapidement après votre connexion : <b> %password% </b>',
            ],
        ],
    ],
    'register' => [
        'title' => 'Inscription',
    ],
    "files" => "Fichiers autorisés : png, jpg, jpeg, webp, svg, gif",
    "toaster" => [
        "error" => "Erreur",
        "used_pseudo" => "Ce pseudo n'est pas disponible.",
        "used_mail" => "Un compte existe déjà avec cette adresse mail.",
        "not_registered_account" => "Ce compte n'existe pas",
        "password_reset" => "Mot de passe réinitialisé et envoyé à l'adresse %mail%",
        "not_same_pass" => "Les mots de passes ne correspondent pas.",
        "welcome" => "Bienvenue !",
        "user_edited" => "Utilisateur modifié",
        "user_edited_self" => "Modification appliquée.",
        "user_edited_self_nope" => "Impossible d'apporter les modifications",
        "pass_change_faild" => "Impossible d'éditer le mot de passe",
        "impossible" => "Impossible de faire ceci",
        "impossible_user" => "Impossible de supprimer cet utilisateur",
        "user_deleted" => "Utilisateur supprimé",
        "mail_pass_matching" => "Adresse mail ou mot de passe incorrect", //TODO COMBINER COMPTE INEXISTANT ET IDENTIFIANT OU MDP INCORRECT
        "role_added" => "Rôle ajouté",
        "role_edited" => "Rôle modifié",
        "role_deleted" => "Rôle supprimé",
        "blacklisted_pseudo" => "Pseudo interdit", //TODO SUGGESTION REMPLACER PAR LE MEME MESSAGE QUE USED_PSEUDO POUR PLUS DE DISCRÉTION
        "status" => "Status de l'utilisateur changé !",
        "error_add" => "Impossible d'ajouter cet utilisateur",
        "success_add" => "Utilisateur %pseudo% ajouté",
        "edited_not_pass_change" => "Utilisateur mis à jour sans modification de mot de passe",
        "edited_pass_change" => "Utilisateur mis à jour avec modification de mot de passe",
        "load_permissions_error" => "Impossible de charger les permissions du package %package%",
        "load_permissions_success" => "Permissions chargées avec succès !",
        "reset_in_progress" => "Votre demande de réinitialisation est déjà en cours ...",
        "reset_link_not_found" => "Ce lien de réinitialisation n'existe pas !",
        "reset_link_not_available" => "Ce lien de réinitialisation n'est plus valide !",
        "reset_link_log_out" => "Vous ne pouvez pas être connecter pour faire ceci !",
        "reset_link_pass_changed" => "Mot de passe changé !",
        "reset_link_follow_the_link" => "Veuillez suivre le lien que vous avez reçu par mail",
        "reset_link_body_mail_1" => "Réinitialiser votre mot de passe sur ",
        "reset_link_body_mail_2" => "Vous venez de faire une demande de réinitialisation de mot de passe.",
        "reset_link_body_mail_3" => "Voici le lien à suivre pour réaliser ce changement (vous avez 15 minutes pour le faire)",
        "reset_link_body_mail_4" => "Cliquez-ici pour changer mon mot de passe.",
        "reset_link_body_mail_5" => "Si vous n'êtes pas à l'origine de cette demande, ignorez simplement ce mail.",
        "errors" => [
            '2fa' => [
                "toggle" => "Impossible de changer le status 2FA de %pseudo%",
                'regen' => 'Impossible de régénérer la clé 2FA de %pseudo%',
            ],
        ],
        'success' => [
            '2fa' => [
                'toggle' => "Status 2FA de %pseudo% changé avec succès",
                'regen' => 'Clé 2FA de %pseudo% régénérée avec succès',
            ],
        ],
    ],
    'manage' => [
        'title' => 'Gestion des utilisateurs',
        'desc' => 'Gérez les utilisateurs de votre site',
        'card_title_list' => 'Liste des utilisateurs inscrits',
        'card_title_add' => 'Ajouter un utilisateur',
        'edit' => [
            'title' => 'Édition de %pseudo%',
            'about' => 'A propos',
        ],
        'randomPasswordTooltip' => 'Générez un mot de passe aléatoire et sécurisé en un clic. Le mot de passe sera copié dans votre presse papier',
    ],
    'edit' => [
        'title' => 'Utilisateurs | Edition',
        'desc' => 'Editez les comptes de vos utilisateurs',
        'activate_account' => 'Activer le compte',
        'disable_account' => 'Désactiver le compte',
        'delete_account' => 'Supprimer le compte',
        'toaster_success' => 'Le compte a bien été mis à jour !',
        'toaster_pass_error' => 'Une erreur est survenue dans la modification du mot de passe.<br>Les mots de passes ne correspondent pas.',
        'reset_password' => 'Réinitialiser le mot de passe',
    ],
    "blacklist" => [
        "title" => "Pseudo en liste noire",
        "table" => [
            "pseudo" => "Pseudo / Nom",
            "date" => "Date",
            "action" => "Actions",
        ],
        'delete' => [
            'title' => 'Suppression du pseudo ',
            'content' => "Ceci permettra à nouveau à vos utilisateurs d'utiliser ce pseudo.",
        ],
        'edit' => [
            'title' => 'Édition du pseudo ',
        ],
    ],
    "roles" => [
        "manage" => [
            "title" => "Gestion des rôles",
            "desc" => "Gérez les rôles de votre site",
            "add" => "Ajouter un rôle",
            "add_title" => "Rôles | Ajouter",
            "edit_title" => "Edition du rôle ",
            "add_desc" => "Créer un nouveau rôle",
            "edit_desc" => "Modifiez un nouveau rôle",
            "permissions_list" => "Autorisations",
            "add_toaster_success" => "Rôle créé avec succès !",
            "edit_toaster_success" => "Rôle modifié avec succès !",
            "delete_toaster_success" => "Rôle supprimé avec succès ",
            "list_title" => "Liste des rôles",
            "description" => "Description du rôle",
            "name" => "Nom du rôle",
            "weightTips" => "Plus le nombre est élevé plus le rôle est important",
            "delete" => [
                "title" => "Supprimer ",
                "content" => "La suppression d'un rôle est définitif !<br>Aucun retour possible !",
            ],
            "default" => [
                "title" => "Rôle par défaut",
                "tips" => "Définissez ce rôle parmi ceux par défaut. Lors de l'inscription, vos membres se les verront ajouté automatiquement.",
            ],
        ],
        'perms' => [
            'admin_warning' => ' Ce rôle est le plus important. Par conséquent, vous ne pouvez pas le supprimer ou modifier ses permissions !',
            'operator' => 'Cette permission est la plus importante et donne tous les accès sans exception.',
        ],
    ],
    "modal" => [
        "delete" => "Supprimer",
        "delete_message" => "La suppression d'un utilisateur est définitive !<br>Aucun retour en arrière n'est possible !",
    ],
    'delete' => [
        'toaster_error' => 'Vous ne pouvez pas supprimer le compte avec lequel vous êtes connecté.',
        'toaster_success' => 'Le compte a bien été supprimé !',
    ],
    'state' => [
        'toaster_error' => 'Vous ne pouvez pas désactiver le compte avec lequel vous êtes connecté.',
        'toaster_success' => 'Le compte a bien été modifié !',
    ],
    "users" => [
        "user" => "Utilisateur",
        "about" => "A propos",
        "list_button_save" => "Enregistrer",
        "mail" => "Adresse mail",
        "pseudo" => "Pseudo",
        "firstname" => "Prénom",
        "surname" => "Nom",
        "roles" => "Rôles",
        "role" => "Rôle",
        "weight" => "Poids",
        "creation" => "Date de création",
        "last_edit" => "Date de modification",
        "last_connection" => "Dernière connexion au site",
        "role_description" => "Description",
        "role_name" => "Nom",
        "password" => "Mot de passe",
        "password_confirm" => "Confirmation du mot de passe",
        "pass" => "••••••••", //TODO METTRE UN VRAI PLACEHOLDER
        "new_password" => "Modifier le mot de passe",
        "repeat_pass" => "Confirmer le mot de passe",
        "toaster_title" => "Information",
        "toaster_title_error" => "Attention",
        "logout" => "Déconnexion",
        "image" => [
            "title" => "Image de profil",
            "last_update" => "Dernière modification",
            "placeholder_input" => "Choisissez une image de profil",
            "image_alt" => "Image de profil de %username%",
            "reset" => "Réinitialisez l'image",
        ],
        'link_profile' => 'Accéder à mon profil',
        'login_methode' => 'Méthode de connexion',
        '2fa' => [
            'regen_key' => 'Régénérer la clé',
        ],
    ],
    "settings" => [
        "title" => "Paramètres utilisateur",
        "desc" => "Gérez les paramètres de la partie utilisateur",
        "default_picture" => "Image de profil par défaut",
        "visualIdentity" => "Identité visuelle",
        "resetPasswordMethod" => [
            "label" => "Méthode de réinitialisation du mot de passe",
            "tips" => "Définissez la méthode de réinitialisation des mots de passes de vos utilisateurs",
            "options" => [
                0 => "Mot de passe envoyé par mail (non recommandé)",
                1 => "Lien unique envoyé par mail",
            ],
        ],
        'profile_view' => [
            'title' => 'Page profil',
            'label' => 'Choisissez comment afficher la page profil',
            'tips' => "Si vous n'utilisez pas la page profil, nous vous conseillons de la désactiver.",
            'options' => [
                0 => '/profile',
                1 => '/profile/VotrePseudo',
                2 => 'Désactiver la page profil',
            ],
        ],
        'blacklisted' => [
            'pseudo' => [
                'label' => 'Gérer la liste noire des pseudos',
                'hint' => "Vous pouvez facilement interdire des pseudos que vous ne souhaitez pas avoir lors de
                           l'inscription ou lors de la modification d'un pseudo.",
                'goBtn' => 'Gérer les pseudos interdits',
                'title' => 'Ajouter des pseudos à la liste noire',
                'description' => 'Ajouter des pseudos à la liste noire',
                'edit' => [
                    'title' => 'Modifiez un pseudo de la liste noire',
                    'description' => "Édition d'un pseudo de la liste noire",
                    'label' => 'Modification du pseudo %pseudo%',
                ],
                'btn' => 'Ajouter à la liste noire',
                'toasters' => [
                    'add' => [
                        'success' => 'Pseudo %pseudo% ajouté à la liste',
                        'error' => "Impossible d'ajouter le pseudo %pseudo% à la liste",
                    ],
                    'edit' => [
                        'success' => 'Pseudo %pseudo% modifié',
                        'error' => 'Impossible de modifier le pseudo %pseudo%',
                    ],
                    'delete' => [
                        'success' => 'Pseudo supprimé',
                        'error' => 'Impossible de supprimer le pseudo',
                    ],
                ],
            ],
        ],
    ],
    'flush' => [
        'modal' => [
            'warning' => 'Ceci va réinitialiser tous vos rôles ! (sauf Administrateur)',
            'text' => "Flusher les permissions est un outil de débogage souvent utilisé par les développeurs qui souhaitent forcer l'ajout manuel des permissions de leurs fichiers Permissions.php se trouvant dans le dossier Init.",
        ],
    ],
    'oauth' => [
        'manage' => [
            'title' => 'Gestion des oAuth',
            'desc' => 'Gérez les méthodes de connexion oAuth',
            'subtitle' => 'Configuration des méthodes oAuth',
            'enabled' => 'Méthodes active',
            'disabled' => 'Méthodes inactive',
        ],
        'flash' => [
            'saveSettingFailed' => 'Une erreur s\'est produite lors de l\'enregistrement des paramètres.',
            'saved' => 'Paramètres enregistrés avec succès.',
            'accessDenied' => 'Accès refusé.',
            'userInfo' => 'Impossible d\'obtenir les informations de l\'utilisateur.',
            'emailUsed' => 'Cet email est déjà utilisé.',
            'userCreate' => 'Impossible de créer l\'utilisateur.',
            'userOauthCreate' => 'Impossible de créer un utilisateur oAuth.',
        ],
    ],
    'permissions' => [
        'settings' => [
            'manage' => 'Gérer les paramètres',
            'blacklist' => [
                'add' => 'Ajouter',
                'edit' => 'Éditer',
                'delete' => 'Supprimer',
            ],
        ],
        'users' => [
            'manage' => 'Gérer les utilisateurs',
            'edit' => 'Éditer',
            'add' => 'Ajouter',
            'delete' => 'Supprimer',
            'roles' => [
                'manage' => 'Gérer les rôles',
                'add' => 'Ajouter',
                'edit' => 'Éditer',
                'delete' => 'Supprimer',
            ],
        ],
    ],
    'security' => [
        'captcha' => [
            'invalid' => 'Captcha invalide',
        ],
        'connected' => [
            'object' => ' - Nouvelle connexion détectée sur votre compte',
            'body' => 'Bonjour %user_name%. <br>Nous avons détecté une nouvelle connexion à votre compte sur <b>%website%</b>.<br><br>📍 Détails de la connexion :<br>- Date et heure : %date%<br>- Adresse IP : %ip%<br><br>Si vous êtes à l\'origine de cette connexion, vous pouvez ignorer ce message.<br><br><b>⚠️ Si cette connexion ne vient pas de vous, nous vous recommandons fortement de :</b><br>- Changer immédiatement votre mot de passe depuis votre espace personnel.<br>- Vérifier l’activité récente de votre compte pour détecter d’éventuelles actions suspectes.<br>- Activer l’authentification à deux facteurs (2FA) si ce n’est pas encore fait.',
        ],
    ],
    'long_date' => [
        'setting' => [
            'label' => 'Renforcé la sécurité des comptes utilisateur',
            'no' => 'Non (non recommandé)',
            'yes' => 'Oui',
            'small' => 'Pour les utilisateurs sans 2FA, un code de confirmation est envoyé par mail s\'ils ne se sont pas connectés depuis plus de 90 jours (l\'envoi de mail doit être fonctionnel).<br>Ce paramètre déclenche également un mail à chaque connexion.',
        ],
        'toaster' => [
            'title' => 'Verification d\'identité',
            'receive_by_mail' => 'Vous allez recevoir le code par mail',
            'put_the_code' => 'Merci de mettre votre code.',
            'invalid_code' => 'Code invalide.',
            'too_late' => 'Ce code est trop vieux vous avez max 15 min pour le valider',
            'unable_to_create_code' => 'Impossible de créer le code',
        ],
        'mail' => [
            'object' => '%site_name% - Vérification d\'identité',
            'body_1' => 'Nous avons besoin de vérifier votre identité sur ',
            'body_2' => 'Voici le CODE permettant de vérifier qu\'il s\'agisse bien de vous :',
            'body_3' => 'Si vous n\'êtes pas à l\'origine de cette demande, nous vous conseillons de changer votre mot de passe !',
        ],
    ],
    'pages' => [
        'settings' => [
            'general' => [
                'menu' => 'Généraux',
            ],
            'security' => [
                'menu' => 'Sécurité',
            ],
            'blacklist' => [
                'menu' => 'Blacklist',
            ],
        ],
    ],
];
