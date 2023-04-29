<?php

use CMW\Controller\Installer\InstallerController;
use CMW\Manager\Lang\LangManager;

/* @var $lang string */

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" class="bg-cmw-gray">
<head>
    <meta charset="utf-8"/>
    <title><?= LangManager::translate("installation.title") ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="robots" content="NOINDEX, NOFOLLOW">
    <meta name="description" content="<?= LangManager::translate("installation.desc") ?>">

    <link rel="icon" type="image/png"
          href="admin/resources/assets/images/logo/logo_compact.png">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="admin/resources/vendors/fontawesome-free/css/fa-all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="installation/views/assets/css/style.css">
</head>



<body class="bg-cmw-gray flex flex-col min-h-screen">
<img class="w-48 mx-auto py-8" src="admin/resources/assets/images/logo/logo_compact.png" alt="Image introuvable !">

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
                <li class="step <?= $classValue ?>"><p><?= LangManager::translate("installation.steps.$i") ?> <?= $finishStatus ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div id="loader" class="loader top-[45%] z-50 hidden"></div>

<div class="card w-5/6 lg:w-4/6 bg-cmw-gray-sec mx-auto mt-8">
    <div class="card-body" id="body">
