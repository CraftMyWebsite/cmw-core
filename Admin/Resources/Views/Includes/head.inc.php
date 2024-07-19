<?php

header("Cache-Control: max-age=2592000");

/** @var $title */

/** @var $description */

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
          href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Img/favicon.ico"/>
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
    @font-face {  font-family: rubik;  src:url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Assets/Webfonts/Rubik-Light.ttf"); font-weight: 300;  }
    @font-face {  font-family: rubik;  src:url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Assets/Webfonts/Rubik-Regular.ttf"); font-weight: 400;  }
    @font-face {  font-family: rubik;  src:url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Assets/Webfonts/Rubik-Medium.ttf"); font-weight: 500;  }
    @font-face {  font-family: rubik;  src:url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Assets/Webfonts/Rubik-SemiBold.ttf"); font-weight: 600;  }
    @font-face {  font-family: rubik;  src:url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Assets/Webfonts/Rubik-Bold.ttf"); font-weight: 700;  }
    @font-face {  font-family: rubik;  src:url("<?=EnvManager::getInstance()->getValue('PATH_SUBFOLDER')?>Admin/Resources/Assets/Webfonts/Rubik-ExtraBold.ttf"); font-weight: 800;  }
</style>

<style>
    :root {
        --light-primary: #ffffff;
        --light-secondary: #f5f5f6;
        --light-third: #cbd5e1;
        --light-fourth: #e2e8f0;
        --light-text-primary: #334155;
        --light-text-secondary: #9ca3af;
        --light-input-bg: #f9fafb;
        --light-scrollbar : #94a3b8;
        --light-scrollbar-hover : #64748b;
        --light-scrollbar-bg : #e2e8f0;

        --dark-primary: #030712;
        --dark-secondary: #111827;
        --dark-third: #334155;
        --dark-fourth: #1e293b;
        --dark-text-primary: #e5e7eb;
        --dark-text-secondary: #4b5563;
        --dark-input-bg: #374151;
        --dark-scrollbar : #334155;
        --dark-scrollbar-hover : #1e293b;
        --dark-scrollbar-bg : #94a3b8;

        --nav-sky : #435EBE;
        --nav-sky-light : #f2f2f3;
        --nav-sky-dark : #1e293b;
        --nav-sky-text-dark : #435EBE;
    }

    .text-success {color: #0ab312}
    .text-info {color: #1C64F2}
    .text-danger {color: #f3182b}
    .text-warning {color: #f3b518}
    .bg-success {background-color: #0ab312}
    .bg-info {background-color: #1C64F2}
    .bg-danger {background-color: #f3182b}
    .bg-warning {background-color: #f3b518}
</style>

<body>