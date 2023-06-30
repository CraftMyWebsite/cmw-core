<?php

return [
    "profile" => "Profil",
    "login" => [
        "title" => "Connexion",
        "desc" => "Connectez-vous pour accéder au panneau d'administration",
        "remember" => "Se souvenir de moi",
        "signin" => "Connexion",
        "lost_password" => "J'ai perdu mon mot de passe",
        "register" => "S'inscrire",
        "forgot_password" => [
            "title" => "Mot de passe oublié",
            "desc" => "Récupérez votre mot de passe",
            "btn" => "Confirmation",
            "mail" => [
                "object" => "Votre nouveau mot de passe %site_name%",
                "body" => "Voici votre nouveau mot de passe à changer rapidement après votre connexion : <b> %password% </b>"
            ],
        ],
    ],
    "register" => [
        "title" => "Inscription"
    ],
    "files" => "Fichiers autorisés : png, jpg, jpeg, webp, svg, gif",
    "toaster" => [
        "error" => "Erreur",
        "success" => "Succès",
        "used_pseudo" => "Ce pseudo est déjà pris.",
        "used_mail" => "Cette e-mail est déjà prise.",
        "not_registered_account" => "Ce compte n'existe pas",
        "password_reset" => "Mot de passe réinitialisé et envoyé à l'adresse %mail%",
        "not_same_pass" => "les mots de passes non identiques",
        "welcome" => "Bienvenue !",
        "user_edited" => "Utilisateur modifié",
        "pass_change_faild" => "Impossible d'éditer le mot de passe",
        "impossible" => "Impossible de faire ceci",
        "impossible_user" => "Impossible de supprimé cet utilisateur",
        "user_deleted" => "Utilisateur supprimé",
        "mail_pass_matching" => "Cette combinaison email/mot de passe est erronée",
        "role_added" => "Rôle ajouté",
        "role_edited" => "Rôle modifié",
        "role_deleted" => "Rôle supprimé",
        "blacklisted_pseudo" => "Pseudo interdit",
        "status" => "Status de l'utilisateur changé !",
        "error_add" => "Impossible d'ajouter cet utilisateur",
        "success_add" => "Utilisateur %pseudo% ajouté"
    ],
    "manage" => [
        "title" => "Gestion des utilisateurs",
        "desc" => "Gérez les utilisateurs de votre site",
        "card_title_list" => "Liste des utilisateurs inscrits",
        "card_title_add" => "Ajouter un utilisateur",
        "edit" => [
            "title" => "Édition de ",
            "about" => "A propos"
        ],
        "randomPasswordTooltip" => "Générez un mot de passe sécuirsé en un clic. Le mot de passe sera aussi dans votre presse papier",
    ],
    "edit" => [
        "title" => "Utilisateurs | Edition",
        "desc" => "Editez les comptes de vos utilisateurs",
        "activate_account" => "Activer le compte",
        "disable_account" => "Désactiver le compte",
        "delete_account" => "Supprimer le compte",
        "toaster_success" => "Le compte a bien été mis à jours !",
        "toaster_pass_error" => "Une erreur est survenue dans la modification du mot de passe.<br>Les mots de passes ne correspondent pas.",
        "reset_password" => "Réinitialiser le mot de passe"
    ],
    "roles" => [
        "manage" => [
            "title" => "Gestion des rôles",
            "desc" => "Gérez les rôles de votre site",

            "add" => "Ajouter un rôle",
            "add_title" => "Rôles | Ajouter",
            "edit_title" => "Édition du rôle ",
            "add_desc" => "Créer un nouveau rôle sur le site",
            "edit_desc" => "Modifiez un nouveau rôle sur le site",
            "permissions_list" => "Liste des permissions",
            "add_toaster_success" => "Rôle créé avec succès !",
            "edit_toaster_success" => "Rôle modifié avec succès !",
            "delete_toaster_success" => "Rôle supprimé avec succès ",
            "list_title" => "Liste des rôles",
            "description" => "Description du rôle",
            "name" => "Nom du rôle",
            "weightTips" => "Plus le chiffre est haut plus le rôle est important",
            "delete" => [
                "title" => "Vérification",
                "content" => "Vous êtes sur le point de supprimé un rôle, êtes-vous sûr ?"
            ],
            "default" => [
                "title" => "Rôle par défaut",
                "tips" => "Definissez ce rôle parmis les rôles par défaut. Lors de l'inscription, vos membres se verront ajouter automatiquement les rôles par défaut."
            ],
        ],
    ],
    "modal" => [
        "delete" => "Supprimer",
        "delete_message" => "The deletion of this user is permanent!<br>No return possible!",
    ],
    "delete" => [
        "toaster_error" => "Vous ne pouvez pas supprimer le compte avec lequel vous êtes connecté.",
        "toaster_success" => "Le compte a bien été supprimé !",
    ],
    "state" => [
        "toaster_error" => "Vous ne pouvez pas désactiver le compte avec lequel vous êtes connecté.",
        "toaster_success" => "Le compte a bien été modifié !",
    ],
    "users" => [
        "user" => "Utilisateur",
        "about" => "A propos",
        "list_button_save" => "Enregistrer",
        "mail" => "Email",
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
        "password_confirm" => "Confirmation mot de passe",
        "pass" => "••••••••",
        "new_password" => "Modifier le mot de passe",
        "repeat_pass" => "Retaper le mot de passe",
        "toaster_title" => "Information",
        "toaster_title_error" => "Attention",
        "logout" => "Déconnexion",
        "image" => [
            "title" => "Image de profile",
            "last_update" => "Dernière modification",
            "placeholder_input" => "Choisissez une image de profile",
            "image_alt" => "Image de profile de %username%",
            "reset" => "Réinitialisez l'image"
        ],
        "link_profile" => "Accéder à mon profil",
    ],
    "settings" => [
        "title" => "Paramètres utilisateurs",
        "desc" => "Gérez les paramètres de la partie utilisateur de votre site",
        "default_picture" => "Image de profil par défaut",
        "visualIdentity" => "Identité visuelle",
        "resetPasswordMethod" => [
            "label" => "Méthode de réinitialisation du mot de passe",
            "tips" => "Définissez la méthode de réinitialisation des mots de passes de vos utilisateurs",
            "options" => [
                0 => "Mot de passe envoyé par mail",
                1 => "Lien unique envoyé par mail"
            ],
        ],
        "profile_view" => [
            "title" => "Page profil",
            "label" => "Choisissez comment afficher la page profil",
            "tips" => "Si vous n'utilisez pas la page profil, nous vous conseillons de la désactiver.",
            "options" => [
                0 => "/profile",
                1 => "/profile/VotrePseudo",
                2 => "Désactiver la page profil"
            ],
        ],
        "blacklisted" => [
            "pseudo" => [
                "label" => "Gérez les pseudos blacklist",
                "hint" => "Vous pouvez facilement interdir des pseudos que vous ne souhaitez pas avoir lors de
                           l'inscription ou lors de la modification d'un pseudo.",
                "goBtn" => "Gérer les pseudos interdits",
                "title" => "Ajoutez des pseudos à votre liste",
                "description" => "Blacklistez des pseudos",
                "edit" => [
                    "title" => "Modifiez un pseudo blacklist",
                    "description" => "Édition d'un pseudo blacklist",
                    "label" => "Modification du pseudo %pseudo%",
                ],
                "btn" => "Blacklister ce pseudo",
                "toasters" => [
                    "add" => [
                        "success" => "Pseudo %pseudo% ajouté à la liste",
                        "error" => "Impossible d'ajouter le pseudo %pseudo% à la liste",
                    ],
                    "edit" => [
                        "success" => "Pseudo %pseudo% modifié",
                        "error" => "Impossible de modifier le pseudo %pseudo%",
                    ],
                    "delete" => [
                        "success" => "Pseudo supprimé",
                        "error" => "Impossible de supprimer le pseudo"
                    ],
                ],
            ],
        ]
    ],
];