<?php

header('Cache-Control: max-age=2592000');

/**
 * @var $title
 */

/**
 * @var $description
 */

use CMW\Manager\Env\EnvManager;
use CMW\Utils\Website;

?>
<!DOCTYPE html>
<html lang="<?= EnvManager::getInstance()->getValue('LOCALE') ?>">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= Website::getWebsiteName() ?> - Admin | <?= $title ?? Website::getTitle(useSiteName: false) ?></title>
    <meta name="description" content="<?= $description ?? Website::getDescription() ?>">
    <meta name="robots" content="NOINDEX, NOFOLLOW">
    <link rel="icon" type="image/x-icon"
          href="<?= Website::getFavicon() ?>"/>
    <link rel="stylesheet"
          href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Css/style.css"/>
    <link rel="stylesheet"
          href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Choices.js/choices.css"/>
    <link rel="stylesheet"
          href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Izitoast/iziToast.min.css"/>
    <link rel="stylesheet"
          href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css"/>

</head>

<script>
    if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark')
    }
</script>

<style>
    @font-face {  font-family: rubik;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Webfonts/Rubik-Light.ttf"); font-weight: 300;  }
    @font-face {  font-family: rubik;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Webfonts/Rubik-Regular.ttf"); font-weight: 400;  }
    @font-face {  font-family: rubik;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Webfonts/Rubik-Medium.ttf"); font-weight: 500;  }
    @font-face {  font-family: rubik;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Webfonts/Rubik-SemiBold.ttf"); font-weight: 600;  }
    @font-face {  font-family: rubik;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Webfonts/Rubik-Bold.ttf"); font-weight: 700;  }
    @font-face {  font-family: rubik;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Webfonts/Rubik-ExtraBold.ttf"); font-weight: 800;  }
</style>

<style>
    :root {
        /* Light mode colors */
        --light-primary: #ffffff;
        --light-secondary: #f8f9fa;
        --light-third: #e9ecef;
        --light-fourth: #dee2e6;
        --light-text-primary: #212529;
        --light-text-secondary: #6c757d;
        --light-input-bg: #ffffff;
        --light-scrollbar: #adb5bd;
        --light-scrollbar-hover: #6c757d;
        --light-scrollbar-bg: #f1f3f5;

        /* Dark mode colors */
        --dark-primary: #0d1117;
        --dark-secondary: #161b22;
        --dark-third: #21262d;
        --dark-fourth: #30363d;
        --dark-text-primary: #e3e4e8;
        --dark-text-secondary: #8b949e;
        --dark-input-bg: #21262d;
        --dark-scrollbar: #484f58;
        --dark-scrollbar-hover: #656c76;
        --dark-scrollbar-bg: #161b22;

        /* Accent colors */
        --nav-sky: #0969da;
        --nav-sky-light: #dbeafe;
        --nav-sky-dark: #21262d;
        --nav-sky-text-dark: #58a6ff;
    }

    .text-success {color: #1a7f37}
    .text-info {color: #0969da}
    .text-danger {color: #cf222e}
    .text-warning {color: #bf8700}
    .bg-success {background-color: #1a7f37}
    .bg-info {background-color: #0969da}
    .bg-danger {background-color: #cf222e}
    .bg-warning {background-color: #bf8700}
</style>

<body>