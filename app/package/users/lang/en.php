<?php

return [
	"login" => [
		"title" => "Login",
		"desc" => "Login to access the administration panel",
		"remember" => "Remember me",
		"signin" => "Login",
		"lost_password" => "I have lost my password",
		"register" => "Register",
        "forgot_password" => [
            "title" => "Forgot your password",
            "desc" => "Retrieve a new password",
            "btn" => "Request new password",
            "mail" => [
                "object" => "This is your new password %site_name%",
                "body" => "This is your new password, please change this password fast <b> %password% </b>"
            ],
        ],
	],
	"list" => [
		"title" => "Users | List",
		"desc" => "List of users registered on the site",
		"card_title" => "List of registered users",
	],
	"roles" => [
		"list_card_title" => "List of roles",
	],
	"edit" => [
		"title" => "Users | Edition",
		"desc" => "Edit the accounts of your users",
		"activate_account" => "Activate the account",
		"disable_account" => "Deactivate the account",
		"delete_account" => "Delete account",
		"toaster_success" => "The account has been updated !",
		"toaster_pass_error" => "An error occurred in changing the password.<br>The passwords do not match.",
        "reset_password" => "Reset password"
	],
	"add" => [
		"title" => "Users | Add",
		"desc" => "Create a new user on the site",
		"card_title" => "Add a user",
	],
	"role" => [
		"add" => "Add a role",
		"add_title" => "Roles | Add",
		"edit_title" => "Roles | Editing",
		"add_desc" => "Create a new role on the site",
		"edit_desc" => "Edit a role on the site",
		"permissions_list" => "List of all permissions",
		"add_toaster_success" => "The role has been created !",
		"edit_toaster_success" => "The role has been edited !",
		"delete_toaster_success" => "The role has been deleted !",
        "list_title" => "Roles list",
        "description" => "Role description",
        "name" => "Role name",
	],
	"delete" => [
		"toaster_error" => "You cannot delete the account you are logged in with.",
		"toaster_success" => "The account has been deleted!",
	],
	"state" => [
		"toaster_error" => "You cannot deactivate the account you are logged in with.",
		"toaster_success" => "The account has been modified!",
	],
	"users" => [
		"user" => "User",
		"about" => "About",
		"list_button_save" => "save",
		"mail" => "Email",
		"pseudo" => "Pseudo",
		"firstname" => "First name",
		"surname" => "Last name",
		"role" => "Roles",
		"weight" => "Weight",
		"creation" => "Creation date",
		"last_edit" => "Modification date",
		"last_connection" => "Last login to the site",
		"role_description" => "Description",
		"role_name" => "Name",
		"pass" => "Password",
		"new_pass" => "Change your password",
		"repeat_pass" => "Retype password",
		"toaster_title" => "Information",
		"toaster_title_error" => "Warning",
		"logout" => "Logout",
        "image" => [
            "title" => "Profile picture",
            "last_update" => "Last update",
            "placeholder_input" => "Choose the profile picture",
            "image_alt" => "Profile picture of %username%",
            "reset" => "Reset image"
        ],
        "link_profile" => "Go to my profile",
	],
    "settings" => [
        "title" => "Users settings",
        "desc" => "Manage your users area settings",
        "default_picture" => "Default profile picture",
        "resetPasswordMethod" => [
            "label" => "Reinitialisation password method",
            "options" => [
                "0" => "New password sent by mail",
                "1" => "Unique link sent by mail"
            ],
        ],
    ],
];