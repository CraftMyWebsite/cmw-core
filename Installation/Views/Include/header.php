<?php

use CMW\Controller\Installer\InstallerController;
use CMW\Manager\Lang\LangManager;

/* @var $lang string */

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" class="bg-cmw-gray">
<head>
    <meta charset="utf-8"/>
    <title><?= LangManager::translate("Installation.title") ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="robots" content="NOINDEX, NOFOLLOW">
    <meta name="description" content="<?= LangManager::translate("Installation.desc") ?>">

    <link rel="icon" type="image/png"
          href="Admin/Resources/Assets/Img/favicon.ico">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="Installation/Views/Assets/Css/style.css">
</head>



<body class="bg-cmw-gray flex flex-col min-h-screen">
<img class="w-48 mx-auto py-8" src="Admin/Resources/Assets/Img/logo_compact.png" alt="Image introuvable !">

<div class="lg:hidden text-center p-4 bg-primary text-xl"><span class="font-bold">
        <?= InstallerController::getInstallationStep() ?></span>
    <span class="text-sm">/<?= count(InstallerController::$installSteps) ?></span></div>

<div class="lg:block w-full mx-auto">
    <div class=" p-4">
        <ul class=" content-center steps steps-horizontal w-full">
            <?php foreach (InstallerController::$installSteps as $i => $step): ?>
                <?php
                $classValue = '';
                $finishStatus = '';

                if (InstallerController::getInstallationStep() >= $i) {
                    $classValue = 'step-primary';
                }
                if (InstallerController::getInstallationStep() > $i) {
                    $finishStatus = '<i class="text-green-500 fa-solid fa-check"></i>';
                }
                if (InstallerController::getInstallationStep() === $i) {
                    $classValue .= ' font-bold';
                }
                ?>
                <li class="step <?= $classValue ?>"><p><?= LangManager::translate("Installation.steps.$i") ?> <?= $finishStatus ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div id="loader" class="loader top-[45%] z-50 hidden"></div>

<div class="card w-5/6 lg:w-4/6 bg-cmw-gray-sec mx-auto mt-8">
    <div class="card-body" id="body">
        <select class="absolute top-0 right-0 select select-ghost select-sm w-32" id="lang" onchange="changeLang(this.value)">
            <option <?= $lang === 'fr' ? 'selected' : '' ?> value="fr">Français</option>
            <option <?= $lang === 'en' ? 'selected' : '' ?> value="en">English</option>
        </select>

