<?php

return [
    "title" => "CraftMyWebsite | Installation",
    "desc" => "Installation of the CraftMyWebsite CMS",
    "welcome" => [
        "title" => "Welcome",
        "subtitle" => "Thank to use CraftMyWebsite for your website !",
        "config" => [
            "title" => "Let's take a tour of your configuration",
        ],
        "content" => "<p>If your configuration is not correct, please refer to the <a class='text-primary' href='' target='_blank'>prerequisites</a>
                        before continuing with the installation.</p>
                        <p>In the event of a request for support from CraftMyWebsite, this information may be useful to us to determine
                        the environment you are in. Please write down any information that you do not know.</p>
                        <p>Now let's move on to setting up your new site ...</p>"
    ],

    "config" => [
        "title" => "Configuration",
        "db" => [
            "db" => "Database",
            "name" => "Name",
            "login" => "Login",
            "name_about" => "Usually <code>localhost</code>. If localhost does not work, please ask your web host for the information.",
            "port" => "Port",
            "address" => "Address",
            "pass" => "Password"
        ],
        "settings" => [
            "settings" => "Settings",
            "devmode" => "Enable developer mode",
            "devmode_about" => "WARNING: Do not use this option unless you know what you are doing, as checking it unnecessarily can lead to vulnerabilities on your website. It is not recommended to activate this option for a site in production.",
            "site_folder" => "Installation folder",
            "site_folder_about" => "Usually <code>/</code>. If CraftMyWebsite is in a folder, please specify <code>/folder/</code>."
        ]
    ],

    "details" => [
        "title" => "Details",
        "website" =>  [
            "name" => "Website name",
            "description" => "Description",
            "description_placeholder" => "Discover my brand new custom website made with CraftMyWebsite"
        ]
    ],

    "administrator" => [
        "title" => "Administrator account",
    ],

    "steps" => [
        0 => "Welcome",
        1 => "Configuration",
        2 => "Details",
        3 => "Bundles",
        4 => "Packages",
        5 => "Themes",
        6 => "Administrator",
        7 => "Finish",
    ],
];