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
	"list" => [
		"title" => "Utilisateurs | Liste",
		"desc" => "Liste des utilisateurs inscrits sur le site",
		"card_title" => "Liste des utilisateurs inscrits",
	],
	"roles" => [
		"list_card_title" => "Liste des rôles",
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
	"add" => [
		"title" => "Utilisateurs | Ajouter",
		"desc" => "Créer un nouvel utilisateur sur le site",
		"card_title" => "ajouter un utilisateur",
	],
	"role" => [
		"add" => "Ajouter un rôle",
		"add_title" => "Rôles | Ajouter",
		"edit_title" => "Rôles | Édition",
		"add_desc" => "Créer un nouveau rôle sur le site",
		"edit_desc" => "Modifiez un nouveau rôle sur le site",
		"permissions_list" => "Liste des permissions",
		"add_toaster_success" => "Rôle créé avec succès !",
		"edit_toaster_success" => "Rôle modifié avec succès !",
		"delete_toaster_success" => "Rôle supprimé avec succès ",
        "list_title" => "Liste des rôles",
        "description" => "Description du rôle",
        "name" => "Nom du rôle"
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
		"pseudo" => "Surnom",
		"firstname" => "Prénom",
		"surname" => "Nom",
		"role" => "Rôles",
		"weight" => "Poids",
		"creation" => "Date de création",
		"last_edit" => "Date de modification",
		"last_connection" => "Dernière connexion au site",
		"role_description" => "Description",
		"role_name" => "Nom",
		"pass" => "Mot de passe",
		"new_pass" => "Modifier le mot de passe",
		"repeat_pass" => "Retaper le mot de passe",
		"toaster_title" => "Information",
		"toaster_title_error" => "Attention",
		"logout" => "Déconnexion",
        "image" => [
            "title" => "Image de profile",
            "last_update" => "Dernière modification",
            "placeholder_input" => "Choisissez une image de profile",
            "image_alt" => "Image de profile de ",
            "reset" => "Réinitialisez l'image"
        ],
	],
    "settings" => [
        "title" => "Paramètres utilisateurs",
        "desc" => "Gérez les paramètres de la partie utilisateur de votre site",
        "default_picture" => "Image de profil par défaut",
    ],
];