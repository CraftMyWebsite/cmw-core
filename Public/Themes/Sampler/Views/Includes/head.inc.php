<?php

use CMW\Controller\Core\CoreController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Utils\Website;

/* @var string $title */
/* @var string $description */
/* @var array $includes */

$siteName = Website::getWebsiteName();

?>
    <!DOCTYPE html>
<html lang="<?= EnvManager::getInstance()->getValue('LOCALE') ?>">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <meta property="og:title" content=<?= $siteName ?>>
        <meta property="og:site_name" content="<?= $siteName ?>">
        <meta property="og:description" content="<?= Website::getWebsiteDescription() ?>">
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="<?= EnvManager::getInstance()->getValue('PATH_URL') ?>">

        <!-- CUSTOM HEADERS -->
        <?= Website::getCustomHeader() ?>

        <title><?= Website::getTitle() ?></title>
        <meta name="description" content="<?= Website::getDescription() ?>">

        <meta name="author" content="CraftMyWebsite, <?= $siteName ?>">
        <meta name="publisher" content="<?= $siteName ?>">
        <meta name="copyright" content="CraftMyWebsite, <?= $siteName ?>">
        <meta name="robots" content="follow, index, all"/>

        <!-- Core theme CSS (Includes Bootstrap)-->
        <link
            href="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Public/Themes/Sampler/Resources/Assets/Css/main.css"
            rel="stylesheet"/>

        <?php
        View::loadInclude($includes, "styles");
        ?>

        <?= ImagesManager::getFaviconInclude() ?>
    </head>
<body id="page-top">
<?php View::loadInclude($includes, "beforeScript", "beforePhp"); ?>
<?= CoreController::getInstance()->cmwWarn() ?>