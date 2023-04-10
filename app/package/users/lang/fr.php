<?php

return [
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
    "toaster" => [
        "error" => "Erreur",
        "success" => "Succès",
        "used_pseudo" => "Ce pseudo est déjà pris.",
        "used_mail" => "Cette e-mail est déjà prise.",
        "not_same_pass" => "les mots de passes non identiques",
        "welcome" => "Bienvenue !",
        "user_edited" => "L'utilisateur viens d'être éditer",
        "pass_change_faild" => "Impossible d'éditer le mot de passe",
        "impossible" => "Impossible de faire ceci",
        "impossible_user" => "Impossible de supprimé cet utilisateur",
        "user_deleted" => "Utilisateur supprimé",
        "mail_pass_matching" => "Cette combinaison email/mot de passe est erronée",
        "role_added" => "Rôle ajouté",
        "role_edit" => "Rôle modifié",
        "role_deleted" => "Rôle supprimé"
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
        ],
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
            "label" => "Méthode de réinitialisations du mot de passe",
            "tips" => "Définissez la méthode de réinitialisation des mots de passes de vos utilisateurs",
            "options" => [
                "0" => "Mot de passe envoyé par mail",
                "1" => "Lien unique envoyé par mail"
            ],
        ],
    ],
];