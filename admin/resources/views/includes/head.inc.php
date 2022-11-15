<?php

use CMW\Utils\Images;

?>
<!DOCTYPE html>
<html lang="fr-FR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CraftMyWebsite | <?=$title?></title>
    <meta name="description" content="<?=$description?>">
    <meta name="robots" content="NOINDEX, NOFOLLOW">

    <script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/js/initTheme.js"></script>
    
    <!--EXTENSION-->
    <link rel="stylesheet" href="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/extensions/choices.js/public/assets/styles/choices.css"/>
    <!--IMPORT BASIQUE-->
    <link rel="stylesheet" href="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/css/main/app.css" />
    <link rel="stylesheet" href="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/css/main/app-dark.css" />
    <link rel="icon" type="image/x-icon" href="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/images/logo/favicon.ico"/>
    <script src="https://kit.fontawesome.com/eced519d56.js" crossorigin="anonymous"></script>
    <script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/js/initTheme.js"></script>

    <link rel="stylesheet" href="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/css/pages/summernote.css" />
    <link rel="stylesheet" href="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/extensions/summernote/summernote-lite.css"/>

    <link rel="stylesheet" href="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/extensions/simple-datatables/style.css"/>
    <link rel="stylesheet" href="<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/css/pages/simple-datatables.css" />

</head>

<style>

    @font-face {
        font-family: Nunito;
        src:url("<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/webfonts/nunito/Nunito-Light.ttf");
        font-weight: 300;
    }
    @font-face {
        font-family: Nunito;
        src:url("<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/webfonts/nunito/Nunito-Regular.ttf");
        font-weight: 400;
    }
    @font-face {
        font-family: Nunito;
        src:url("<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/webfonts/nunito/Nunito-Medium.ttf");
        font-weight: 500;
    }
    @font-face {
        font-family: Nunito;
        src:url("<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/webfonts/nunito/Nunito-SemiBold.ttf");
        font-weight: 600;
    }
    @font-face {
        font-family: Nunito;
        src:url("<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/webfonts/nunito/Nunito-Bold.ttf");
        font-weight: 700;
    }
    @font-face {
        font-family: Nunito;
        src:url("<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/webfonts/nunito/Nunito-ExtraBold.ttf");
        font-weight: 800;
    }
    @font-face {
        font-family: "summernote";
        font-style: normal;
        font-weight: 400;
        font-display: auto;
        src: url("<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/extensions/summernote/font/summernote.eot?#iefix") format("embedded-opentype"), url("<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/extensions/summernote/font/summernote.woff2") format("woff2"), url("<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/extensions/summernote/font/summernote.woff") format("woff"), url("<?=getenv("PATH_SUBFOLDER")?>admin/resources/assets/extensions/summernote/font/summernote.ttf") format("truetype");
    }
</style>

<body>

<div id="app">