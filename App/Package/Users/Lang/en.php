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
                'object_pass' => 'This is your new password %site_name%',
                'object_link' => 'Change your password on %site_name%',
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
        "reset_in_progress" => "Your reset request is already in progress...",
        "reset_link_not_found" => "This reset link does not exist!",
        "reset_link_not_available" => "This reset link is no longer valid!",
        "reset_link_log_out" => "You can't be logged in to do this!",
        "reset_link_pass_changed" => "Password changed!",
        "reset_link_follow_the_link" => "Please follow the link you received by email",
        "reset_link_body_mail_1" => "Reset your password on ",
        "reset_link_body_mail_2" => "You have just requested a password reset.",
        "reset_link_body_mail_3" => "Here is the link to follow to make this change (you have 15 minutes to do it)",
        "reset_link_body_mail_4" => "Click here to change my password.",
        "reset_link_body_mail_5" => "If you are not the originator of this request, simply ignore this email.",
        "errors" => [
            '2fa' => [
                "toggle" => "Unable to change 2FA status for %pseudo%",
                'regen' => "Unable to regenerate 2FA secret for %pseudo%",
            ],
        ],
        'success' => [
            '2fa' => [
                'toggle' => "2FA status changed for %pseudo%",
                'regen' => "2FA secret regenerated for %pseudo%",
            ],
        ],
    ],
    'manage' => [
        'title' => 'Manage users',
        'desc' => 'Manage your website users',
        'card_title_list' => 'List of registered users',
        'card_title_add' => 'Add a new user',
        'edit' => [
            'title' => 'Editing of %pseudo%',
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
        '2fa' => [
            'regen_key' => 'Regenerate the key',
        ],
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
        'connected' => [
            'object' => ' - New login detected on your account',
            'body' => 'Hello %user_name%. <br>We have detected a new login to your account on <b>%website%</b>.<br><br>📍 Login details:<br>- Date and time: %date%<br>- IP address: %ip%<br><br>If you initiated this login, you can ignore this message.<br><br><b>⚠️ If this login was not made by you, we strongly recommend that you:</b><br>- Immediately change your password from your personal account settings.<br>- Check your recent account activity for any suspicious actions.<br>- Enable two-factor authentication (2FA) if you haven’t already.',
        ],
    ],
    'long_date' => [
        'setting' => [
            'label' => 'Strengthened user account security',
            'no' => 'No (not recommended)',
            'yes' => 'Yes',
            'small' => 'For users without 2FA, a confirmation code is sent by email if they haven\'t logged in for over 90 days (email delivery must be functional).<br>This setting also triggers an email for each login.',
        ],
        'toaster' => [
            'title' => 'Identity verification',
            'receive_by_mail' => 'You will receive the code by email',
            'put_the_code' => 'Please enter your code.',
            'invalid_code' => 'Invalid code.',
            'too_late' => 'This code is too old, you have max 15 min to validate it',
            'unable_to_create_code' => 'Unable to create code',
        ],
        'mail' => [
            'object' => '%site_name% - Identity verification',
            'body_1' => 'We need to verify your identity on ',
            'body_2' => 'Here is the CODE to verify that it is you:',
            'body_3' => 'If you are not the originator of this request, we advise you to change your password!',
        ],
    ],
    'pages' => [
        'settings' => [
            'general' => [
                'menu' => 'General',
            ],
            'security' => [
                'menu' => 'Security',
            ],
            'blacklist' => [
                'menu' => 'Blacklist',
            ],
        ],
    ],
];
