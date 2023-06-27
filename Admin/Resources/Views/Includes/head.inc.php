<?php

/** @var $title */

/** @var $description */

use CMW\Manager\Env\EnvManager;

?>
<!DOCTYPE html>
<html lang="<?= EnvManager::getInstance()->getValue('LOCALE') ?>">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>CraftMyWebsite | <?= $title ?></title>
    <meta name="description" content="<?= $description ?>">
    <meta name="robots" content="NOINDEX, NOFOLLOW">

    <script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Js/darkMode.js"></script>

    <!--IMPORT BASIQUE-->
    <link rel="stylesheet"
          href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Css/Main/app.css"/>
    <link rel="stylesheet"
          href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Css/Main/app-dark.css"/>
    <link rel="icon" type="image/x-icon"
          href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Images/Logo/favicon.ico"/>
    <link rel="stylesheet"
          href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css"/>
    <link rel="stylesheet"
          href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Choices.js/Public/Assets/Styles/choices.css"/>

</head>

<style>

    @font-face {
        font-family: Nunito;
        src: url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Assets/Webfonts/Nunito/Nunito-Light.ttf");
        font-weight: 300;
    }

    @font-face {
        font-family: Nunito;
        src: url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Assets/Webfonts/Nunito/Nunito-Regular.ttf");
        font-weight: 400;
    }

    @font-face {
        font-family: Nunito;
        src: url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Assets/Webfonts/Nunito/Nunito-Medium.ttf");
        font-weight: 500;
    }

    @font-face {
        font-family: Nunito;
        src: url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Assets/Webfonts/Nunito/Nunito-SemiBold.ttf");
        font-weight: 600;
    }

    @font-face {
        font-family: Nunito;
        src: url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Assets/Webfonts/Nunito/Nunito-Bold.ttf");
        font-weight: 700;
    }

    @font-face {
        font-family: Nunito;
        src: url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Assets/Webfonts/Nunito/Nunito-ExtraBold.ttf");
        font-weight: 800;
    }

    @font-face {
        font-family: "summernote";
        font-style: normal;
        font-weight: 400;
        font-display: auto;
        src: url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Vendors/Summernote/Font/summernote.eot?#iefix") format("embedded-opentype"), url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Vendors/Summernote/Font/summernote.woff2") format("woff2"), url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Vendors/Summernote/Font/summernote.woff") format("woff"), url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Vendors/Summernote/Font/summernote.ttf") format("truetype");
    }
</style>

<body>
<script>
    const theme = localStorage.getItem('theme') || 'theme-dark';
    document.body.className = theme;
  </script>
<div id="app">