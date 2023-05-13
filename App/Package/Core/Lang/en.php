<?php

return [
    "home" => "Home",
    "cgu" => "CGU",
    "cgv" => "CGV",
    "general" => "General",
    "package" => "Package",
    "packages" => "Packages",
    "themes" => "Themes",
    "alt" => [
        "logo" => "Logo CMW",
    ],
    "condition" => [
        "title" => "Terms and conditions",
        "cgv" => "General condition of sale (CGV)",
        "cgu" => "General conditions of use (CGU)",
        "activecgv" => "Enable this terms",
        "activecgu" => "Enable this terms",
        "content" => "Content :",
        "updateby" => "Updated by",
        "on" => "on",
    ],
    "dashboard" => [
        "title" => "Dashboard",
        "desc" => "Welcome to your administration panel!",
        "total_member" => "Total Members",
        "best_views" => "Record of Visits",
        "numbers_views" => "Number of Visits and inscriptions",
        "monthly_visits" => "Monthly Visits",
        "total_visits" => "Total Visits",
        "welcome" => "Welcome",
        "site_info" => "Site Information",
        "name" => "Name :",
        "description" => "Description :",
        "edit" => "Modify this information",
    ],
    "menus" => [
        "title" => "Menus",
        "desc" => "Edit main site menu",
        "add" => [
            "name" => "Nom of the menu",
            "name_hint" => "Votes",
            "targetBlank" => "Open link in new tab",
            "choice" => "Type of link",
            "package" => "Package",
            "package_select" => "Select the package",
            "custom" => "Custom",
            "custom_hint" => "https://store.monsite.fr",
            "allowedGroups" => "Allow some groups to access this menu",
            "group_select" => "Select roles",
            "toaster" => [
                "success" => "Menu added with success",
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
        "title" => "Configuration",
        "desc" => "Configure your CMW website !",
        "favicon" => "Edit your website favicon",
        "favicon_tips" => 'CraftMyWebsite allow only <a href="https://www.icoconverter.com" target="_blank">.ico</a> for better performances.',
        "dateFormat" => "Date formatting",
        "dateFormatTooltip" => "You can customise the way you display the dates",
        "custom" => "-- Customize-moi --",
    ],
    "Lang" => [
        "title" => "Languages",
        "desc" => "Configure the languages of your CMW website !",
        "change" => "Change the website locale",
    ],
    "website" => [
        "name" => "Website name",
        "description" => "Website description",
    ],
    "minecraft" => [
        "ip" => "IP Address of your server",
        "register" => "Allow only Minecraft PREMIUM accounts to register on you website",
    ],
    "database" => [
        "error" => "Database error: ",
    ],
    "toaster" => [
        "success" => "Success",
        "warning" => "Warning",
        "error" => "Error",
        "internalError" => "Internal error",
        "config" => [
            "success" => "Configuration edit with success !",
        ],
        "mail" => [
            "test" => "Mail send to %mail%"
        ],
        "Theme" => [
            "regenerate" => "Theme configuration regenerate"
        ],
        "db" => [
            "config" => [
                "success" => "Configuration good",
                "error" => "Configuration error"
            ],
            "missing_inputs" => "Please fill all inputs !",
        ],
    ],
    "datatables" => [
        "list" => [
            "processing" => "Treatment in progress...",
            "search" => "Search&nbsp;: ",
            "lenghtmenu" => "Showing _MENU_ entries",
            "setlimit" => "Showing _START_ to _END_ of _TOTAL_ entries",
            "info" => "Showing _START_ to _END_ of _TOTAL_ entries",
            "info_vanilla" => "Affichage des &eacute;lements {start} &agrave; {end} sur {rows} &eacute;l&eacute;ments",
            "info_empty" => "Showing 0 to 0 of 0 entries",
            "info_filtered" => "(Filtered of _MAX_ entries)",
            "info_postfix" => "",
            "loadingrecords" => "Loading in progress...",
            "zerorecords" => "No data available in the table",
            "emptytable" => "No data available in the table",
            "first" => "First",
            "previous" => "Previous",
            "next" => "Next",
            "last" => "Last",
            "sort" => [
                "ascending" => ": activate to sort the column in ascending order",
                "descending" => ": activate to sort the column in descending order",
            ],
        ],
    ],
    "Theme" => [
        "config" => [
            "title" => "Manage your themes",
            "description" => "Manage the themes of your website",
            "select" => "Select your Theme",
            "list" => [
                "title" => "Our officials themes",
                "info" => "You can download all this themes on our marketplace",
                "name" => "Name",
                "version" => "Version",
                "cmw_version" => "Version CMW",
                "downloads" => "Downloads",
                "download" => "Download",
            ],
            "regen_config" => "Re-generate Theme config"
        ],
        "manage" => [
            "title" => "Manage your Theme <b>%Theme%</b>",
            "description" => "Manage your Theme to have a Theme that suits you ! "
        ],
    ],
    "mail" => [
        "config" => [
            "title" => "Manage your mails",
            "description" => "Manage your mails settings",
            "enableSMTP" => "Enable SMTP",
            "senderMail" => "Email sender",
            "replyMail" => "Email reply",
            "serverSMTP" => "SMTP address",
            "userSMTP" => "SMTP user",
            "passwordSMTP" => "Password",
            "portSMTP" => "Port SMTP",
            "protocol" => "Sending protocol",
            "footer" => "Mails footer",
            "test" => [
                "btn" => "Try your configuration",
                "title" => "Try now your configuration",
                "warning" => "Remember to save your configuration before starting the test !",
                "description" => "You can test your configuration by sending an email to your email address.",
                "receiverMail" => "Recipient address",
                "receiverMailPlaceholder" => "Enter your mail",
            ]
        ]
    ],
    "downloads" => [
        "errors" => [
            "internalError" => "Internal error with resource %name% - %version%"
        ]
    ],
    "updates" => [
        "title" => "Update your website",
        "description" => "Update your CraftMyWebsite website",
        "errors" => [
            "download" => "Unable to download the latest CMS version.",
            "nullFileUpdate" => "This version doesn't contain any update file.",
            "prepareArchive" => "Unable to prepare archive for update the CMS.",
            "deletedFiles" => "Unable to delete old files.",
            "deleteFile" => "Unable to delete file %file%",
            "sqlUpdate" => "Unable to update your database.",
        ],
        "success" => "Website update with success",
    ],
    "security" => [
        "title" => "CMW - Security",
        "description" => "Manage your website security",
        "captcha" => [
            "title" => "Manage your captcha",
            "type" => "Captcha type",
        ],
    ],
    "footer" => [
        "left" => "Copyright &copy; 2014 - " . date("Y") . " All right reserved.",
        "right" => "Thank you for using <a target='_blank' href='https://craftmywebsite.fr/'>CraftMyWebsite</a>.",
        "used" => "You are using the version ",
        "upgrade" => "Please update to ",
    ],
    "btn" => [
        "save" => "Save",
        "delete" => "Delete",
        "delete_forever" => "Delete forever",
        "close" => "Close",
        "send" => "Send",
        "add" => "Add",
        "edit" => "Edit",
        "action" => "Action",
        "confirm" => "Confirm",
        "next" => "Next",
        "try" => "Try",
        "continue" => "Continue"
    ],
    "months" => '["January","February","March","April","May","June","July","August","September","October","November","December"]',

    "errors" => [
        "requests" => [
            'required' => 'Missing input %key%',
            'empty' => 'Input %key% empty',
            'slug' => 'Invalide slug %key%',
            'minLength' => 'Input %key% need to have more than %min% characters',
            'maxLength' => 'Input %key% need to have max %max% characters',
            'betweenLength' => 'Input %key% need to have between %min% and %max% characters',
            'dateTime' => 'Input %key% need to be a validate format (%format%)',
            'getValue' => 'Missing value %key%',
            'type' => 'Invalide type for %key%',
        ]
    ]
];