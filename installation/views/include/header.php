<?php

use CMW\Controller\Installer\InstallerController;

/* @var $lang string */

?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" class="bg-cmw-gray">
<head>
    <meta charset="utf-8"/>
    <title><?= INSTALL_TITLE ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="robots" content="NOINDEX, NOFOLLOW">
    <meta name="description" content="<?= INSTALL_DESC ?>">

    <link rel="icon" type="image/png"
          href="admin/resources/assets/images/logo/logo_compact.png">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="admin/resources/vendors/fontawesome-free/css/fa-all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="installation/views/assets/css/style.css">
</head>

<div id="loader" class="loader top-[45%] z-50"></div>

<body class="bg-cmw-gray">
<img class="w-48 mx-auto py-8" src="admin/resources/assets/images/logo/logo_compact.png" alt="Image introuvable !">

<div class="lg:hidden text-center p-4 bg-primary text-xl"><span class="font-bold">2</span><span
            class="text-sm">/8</span></div>


<div class="hidden lg:block w-full mx-auto">
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
                <li class="step <?= $classValue ?>"><p><?= constant('INSTALL_STEP_' . $i) ?> <?= $finishStatus ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<div class="card w-5/6 lg:w-4/6 bg-cmw-gray-sec mx-auto mt-8">
    <div class="card-body">
