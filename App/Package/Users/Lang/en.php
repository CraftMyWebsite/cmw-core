<?php

return [
    'profile' => 'Profil',
    'login' => [
        'title' => 'Login',
        'desc' => 'Login to access the administration panel',
        'remember' => 'Remember me',
        'signin' => 'Login',
        'lost_password' => 'I have lost my password',
        'register' => 'Register',
        'forgot_password' => [
            'title' => 'Forgot your password',
            'desc' => 'Retrieve a new password',
            'btn' => 'Request new password',
            'mail' => [
                'object' => 'This is your new password %site_name%',
                'body' => 'This is your new password, please change this password fast <b> %password% </b>',
            ],
        ],
    ],
    'register' => [
        'title' => 'Register',
    ],
    'files' => 'Files allowed : png, jpg, jpeg, webp, svg, gif',
    'toaster' => [
        'error' => 'Error',
        'success' => 'Success',
        'used_pseudo' => 'This username is already taken.',
        'used_mail' => 'This email is already taken.',
        'not_registered_account' => 'This account is not registered',
        'password_reset' => 'Password reset for this address %mail%',
        'not_same_pass' => 'non-identical passwords',
        'welcome' => 'Welcome !',
        'user_edited' => 'User just edited',
        'user_edited_self' => 'Modification applied.',
        'user_edited_self_nope' => 'Unable to apply modification now',
        'pass_change_faild' => 'Unable to edit password',
        'impossible' => "Can't do this",
        'impossible_user' => 'Unable to delete this user',
        'user_deleted' => 'User deleted',
        'mail_pass_matching' => 'This email/password combination is wrong',
        'role_added' => 'Role added',
        'role_edited' => 'Role edited',
        'role_deleted' => 'Role deleted',
        'blacklisted_pseudo' => 'Pseudo blacklisted',
        'status' => 'User status edited !',
        'error_add' => 'Unable to add this user',
        'success_add' => 'User %pseudo% added',
        'edited_not_pass_change' => 'User edited (without changing password)',
        'edited_pass_change' => 'User edited (with password edit)',
        'load_permissions_error' => 'Unable to load package permissions %package%',
        'load_permissions_success' => 'Permissions loaded with success!',
    ],
    'manage' => [
        'title' => 'Manage users',
        'desc' => 'Manage your website users',
        'card_title_list' => 'List of registered users',
        'card_title_add' => 'Add a new user',
        'edit' => [
            'title' => 'Editing of ',
            'about' => 'About  ',
        ],
        'randomPasswordTooltip' => 'Generate a secure random password. The password will be past on your clipboard',
    ],
    'edit' => [
        'title' => 'Users | Edition',
        'desc' => 'Edit the accounts of your users',
        'activate_account' => 'Activate the account',
        'disable_account' => 'Deactivate the account',
        'delete_account' => 'Delete account',
        'toaster_success' => 'The account has been updated !',
        'toaster_pass_error' => 'An error occurred in changing the password.<br>The passwords do not match.',
        'reset_password' => 'Reset password',
    ],
    'blacklist' => [
        'title' => 'Blacklisted username',
        'table' => [
            'pseudo' => 'Username/Name',
            'date' => 'Date',
            'action' => 'Actions',
        ],
        'delete' => [
            'title' => 'Removal of username ',
            'content' => 'This will allow your users to use this username again.',
        ],
        'edit' => [
            'title' => 'Editing username ',
        ],
    ],
    'roles' => [
        'manage' => [
            'title' => 'Manage your roles',
            'desc' => 'Manage your website roles',
            'add' => 'Add a role',
            'add_title' => 'Roles | Add',
            'edit_title' => 'Editing role ',
            'add_desc' => 'Create a new role on the site',
            'edit_desc' => 'Edit a role on the site',
            'permissions_list' => 'Permissions',
            'add_toaster_success' => 'The role has been created !',
            'edit_toaster_success' => 'The role has been edited !',
            'delete_toaster_success' => 'The role has been deleted !',
            'list_title' => 'Roles list',
            'description' => 'Role description',
            'name' => 'Role name',
            'weightTips' => 'Increase the number for a more important role',
            'delete' => [
                'title' => 'Delete ',
                'content' => 'The deletion of this role is permanent!<br>No return possible!',
            ],
            'default' => [
                'title' => 'Default roles',
                'tips' => 'Define your members role(s) when they sign up on your website.',
            ],
        ],
        'perms' => [
            'admin_warning' => ' This role is the most important. Therefore, you cannot delete it or modify its permissions!',
            'operator' => 'This permission is the most important and gives all access without exception.',
        ],
    ],
    'modal' => [
        'delete' => 'Remove',
        'delete_message' => 'La suppression de cet utilisateur est définitive !<br>Aucun retour possible !',
    ],
    'delete' => [
        'toaster_error' => 'You cannot delete the account you are logged in with.',
        'toaster_success' => 'The account has been deleted!',
    ],
    'state' => [
        'toaster_error' => 'You cannot deactivate the account you are logged in with.',
        'toaster_success' => 'The account has been modified!',
    ],
    'users' => [
        'user' => 'User',
        'about' => 'About',
        'list_button_save' => 'save',
        'mail' => 'Email',
        'pseudo' => 'Pseudo',
        'firstname' => 'First name',
        'surname' => 'Last name',
        'roles' => 'Roles',
        'role' => 'Role',
        'weight' => 'Weight',
        'creation' => 'Creation date',
        'last_edit' => 'Modification date',
        'last_connection' => 'Last login to the site',
        'role_description' => 'Description',
        'role_name' => 'Name',
        'password' => 'Password',
        'password_confirm' => 'Confirm password',
        'pass' => '••••••••',
        'new_password' => 'Change your password',
        'repeat_pass' => 'Retype password',
        'toaster_title' => 'Information',
        'toaster_title_error' => 'Warning',
        'logout' => 'Logout',
        'image' => [
            'title' => 'Profile picture',
            'last_update' => 'Last update',
            'placeholder_input' => 'Choose the profile picture',
            'image_alt' => 'Profile picture of %username%',
            'reset' => 'Reset image',
        ],
        'link_profile' => 'Go to my profile',
        'login_methode' => 'Login methode',
    ],
    'settings' => [
        'title' => 'Users settings',
        'desc' => 'Manage your users area settings',
        'default_picture' => 'Default profile picture',
        'visualIdentity' => 'Visual identity',
        'tips' => 'Define your users reset password method',
        'resetPasswordMethod' => [
            'label' => 'Reinitialisation password method',
            'options' => [
                0 => 'New password sent by mail',
                1 => 'Unique link sent by mail',
            ],
        ],
        'profileView' => [
            'title' => 'Profile page',
            'label' => 'Manage how to display your profile page',
            'tips' => "If you don't use the profile page, we suggest you tu disable this feature.",
            'options' => [
                0 => '/profile',
                1 => '/profile/YourPseudo',
                2 => 'Disable the profile page',
            ],
        ],
        'blacklisted' => [
            'pseudo' => [
                'label' => 'Manage blacklisted pseudos',
                'hint' => 'You can blacklisted pseudo from register and pseudo editing',
                'goBtn' => 'Manage blacklisted pseudos',
                'title' => 'Add players to your list',
                'description' => 'Blacklist pseudos',
                'edit' => [
                    'title' => 'Edit blacklisted pseudo',
                    'description' => 'Edit a blacklisted pseudo',
                    'label' => 'Editing pseudo %pseudo%',
                ],
                'btn' => 'Blacklist this pseudo',
                'toasters' => [
                    'add' => [
                        'success' => 'Pseudo %pseudo% added',
                        'error' => 'Unable to add %pseudo%',
                    ],
                    'edit' => [
                        'success' => 'Pseudo %pseudo% edited',
                        'error' => 'Unable to edit pseudo %pseudo%',
                    ],
                    'delete' => [
                        'success' => 'Pseudo deleted',
                        'error' => 'Unable to delete this pseudo',
                    ],
                ],
            ],
        ],
    ],
    'flush' => [
        'modal' => [
            'warning' => 'This will reset all your roles! (except Administrator)',
            'text' => 'Flushing Permissions is a debugging tool often used by developers who want to force the manual addition of permissions to their Permissions.php files located in the Init folder.',
        ],
    ],
    'oauth' => [
        'manage' => [
            'title' => 'Manage oAuth',
            'desc' => 'Manage oAuth methods',
            'subtitle' => 'Manage oAuth methods',
            'enabled' => 'Active methods',
            'disabled' => 'Inactive methods',
        ],
        'flash' => [
            'saveSettingFailed' => 'An error occurred while saving the settings.',
            'saved' => 'Settings saved successfully.',
            'accessDenied' => 'Access denied.',
            'userInfo' => 'Unable to get user information.',
            'emailUsed' => 'This email is already used.',
            'userCreate' => 'Unable to create user.',
            'userOauthCreate' => 'Unable to create oAuth user.',
        ],
    ],
    'permissions' => [
        'settings' => [
            'manage' => 'Manage settings',
            'blacklist' => [
                'add' => 'Add',
                'edit' => 'Edit',
                'delete' => 'Delete',
            ],
        ],
        'users' => [
            'manage' => 'Manage users',
            'edit' => 'Edit',
            'add' => 'Add',
            'delete' => 'Delete',
            'roles' => [
                'manage' => 'Manage roles',
                'add' => 'Add',
                'edit' => 'Edit',
                'delete' => 'Delete',
            ],
        ],
    ],
    'security' => [
        'captcha' => [
            'invalid' => 'invalid Captcha',
        ],
    ],
];
