<?php

return [
    "profile" => "Profile",
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
    "register" => [
        "title" => "Register"
    ],
    "toaster" => [
        "error" => "Error",
        "success" => "Success",
        "used_pseudo" => "This username is already taken.",
        "used_mail" => "This email is already taken.",
        "not_same_pass" => "non-identical passwords",
        "welcome" => "Welcome !",
        "user_edited" => "User just edited",
        "pass_change_faild" => "Unable to edit password",
        "impossible" => "Can't do this",
        "impossible_user" => "Unable to delete this user",
        "user_deleted" => "User deleted",
        "mail_pass_matching" => "This email/password combination is wrong",
        "role_added" => "Role added",
        "role_edit" => "Role edited",
        "role_deleted" => "Role deleted"
    ],
    "manage" => [
        "title" => "Manage users",
        "desc" => "Manage your website users",
        "card_title_list" => "List of registered users",
        "card_title_add" => "Add a new user",
        "edit" => [
            "title" => "Editing of ",
            "about" => "About  "
        ],
        "randomPasswordTooltip" => "Generate a secure random password. The password will be past on your clipboard",
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
    "role" => [
        "manage" => [
            "title" => "Manage your roles",
            "desc" => "Manage your website roles",

            "add" => "Add a role",
            "add_title" => "Roles | Add",
            "edit_title" => "Editing role ",
            "add_desc" => "Create a new role on the site",
            "edit_desc" => "Edit a role on the site",
            "permissions_list" => "List of all permissions",
            "add_toaster_success" => "The role has been created !",
            "edit_toaster_success" => "The role has been edited !",
            "delete_toaster_success" => "The role has been deleted !",
            "list_title" => "Roles list",
            "description" => "Role description",
            "name" => "Role name",
            "weightTips" => "Increase the number for a more important role",
            "delete" => [
                "title" => "Verification",
                "content" => "Are you sure about that, sir ?"
            ],
        ]
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
        "roles" => "Roles",
        "role" => "Role",
        "weight" => "Weight",
        "creation" => "Creation date",
        "last_edit" => "Modification date",
        "last_connection" => "Last login to the site",
        "role_description" => "Description",
        "role_name" => "Name",
        "password" => "Password",
        "password_confirm" => "Confirm password",
        "pass" => "••••••••",
        "new_password" => "Change your password",
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
        "visualIdentity" => "Visual identity",
        "tips" => "Define your users reset password method",
        "resetPasswordMethod" => [
            "label" => "Reinitialisation password method",
            "options" => [
                0 => "New password sent by mail",
                1 => "Unique link sent by mail"
            ],
        ],
        "profileView" => [
            "title" => "Profile page",
            "label" => "Manage how to display your profile page",
            "tips" => "If you don't use the profile page, we suggest you tu disable this feature.",
            "options" => [
                0 => "/profile",
                1 => "/profile/YourPseudo",
                2 => "Disable the profile page"
            ],
        ],
    ],
];