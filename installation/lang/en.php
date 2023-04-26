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
                        <p>Now let's move on to setting up your new site ...</p>",
        "readaccept" => "I have read and accept the",
        "cgu" => "General Conditions of Use"
    ],
    "bundle" => [
        "custom" => "Custom",
        "customText" => "<p>Customize your installation yourself.</p><p>This does not mean that it is not possible to customize it with other bundles.</p>",
    ],
    "password" => [
        "strenght" => "Password strength :",
        "notmatch" => "The passwords do not match!"
    ],
    "search" => "Search",
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
        "website" => [
            "name" => "Website name",
            "description" => "Description",
            "description_placeholder" => "Discover my brand new custom website made with CraftMyWebsite"
        ]
    ],

    "packages" => [
        "title" => "Select your packages",
        "sub_title" => "Click to select",
        "list_title" => "List of packages",
        "free" => "Free",
        "version" => "Version",
        "demo" => "Demo",
        "search" => "Search",
        "tags" => "Tags",
        "help" => [
            "title" => "Customize your installation",
            "content" => "<b>Information:</b> This step allows you in a few clicks to install the package you will have
                need to get your site off to a good start.<br>
                This configuration is not final, it is possible to add others later via your panel
                administration.<br><br>
                <b>Presets: </b>The presets you pre-check the packages most suited to your needs, you can all
                times remove / add others according to your needs.<br>",
            "footer" => "**Hover over a package to learn more"
        ]
    ],

    "themes" => [
        "title" => "Choosing a theme",
        "sub_title" => "Click on image to select",
        "compatibility" => "Compatibility",
        "more" => "More information"
    ],

    "administrator" => [
        "title" => "Administrator account",
    ],

    "finish" => [
        "title" => "Congratulation!",
        "desc" => "Your site is now ready !",
        "review" => "Let's review your configuration together :",
        "version" => "Version CMW :",
        "theme" => "Theme :",
        "bundle" => "Bundle :",
        "package" => "Package :",
        "goToMySite" => "Go to my site",
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