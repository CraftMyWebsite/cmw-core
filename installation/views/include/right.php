<?php
use CMW\Utils\Utils;

$dbHost = Utils::getEnv()->getValue("DB_HOST") ?? "<span class='text-danger'>" . INSTALL_INFOS_ERROR . "</span>";
$dbUsername = Utils::getEnv()->getValue("DB_USERNAME") ?? "<span class='text-danger'>" . INSTALL_INFOS_ERROR . "</span>";
$dbPassword = Utils::getEnv()->getValue("DB_PASSWORD") ? "*****" : "<span class='text-danger'>" . INSTALL_INFOS_EMPTY . "</span>";
$dbName = Utils::getEnv()->getValue("DB_NAME") ?? "<span class='text-danger'>" . INSTALL_INFOS_ERROR . "</span>";
?>

<div class="col-5">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><?= INSTALL_INFOS_TITLE ?></h3>
        </div>
        <div class="card-body">
            <p><?= '<b>' . INSTALL_PHP_VERSION_INFOS . ' PHP :</b> ' . PHP_VERSION ?></p>
            <?php if (PHP_VERSION_ID < 80100) : ?>
                <div class="alert alert-danger alert-dismissible">
                    <p class="info-box-text font-weight-bold"><i
                                class="fas fa-exclamation-triangle"></i> <?= INSTALL_ALERT_VERSION_TITLE ?>
                    </p>
                    <?= INSTALL_ALERT_VERSION_INFOS ?>
                </div>
            <?php endif; ?>
            <?php if (Utils::getEnv()->getValue("installStep") === 0) : ?>
                <div class="info-box bg-success">
                    <span class="info-box-icon"><i class="fas fa-server"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text font-weight-bold"><?= INSTALL_INFOS_SUCCESS ?></span>
                        <span class="progress-description"><?= INSTALL_INFOS_TEXT ?></span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            <?php endif; ?>
            <?php if (Utils::getEnv()->getValue("installStep") > 0) : ?>
                <p>
                    <b><?= INSTALL_BDD_ADDRESS ?> :</b> <?= $dbHost ?><br>
                    <b><?= INSTALL_BDD_USER_ABOUT ?> :</b> <?= $dbUsername ?><br>
                    <b><?= INSTALL_BDD_PASS_ABOUT ?> :</b> <?= $dbPassword ?><br>
                    <b><?= INSTALL_BDD_NAME ?> :</b> <?= $dbName ?>
                </p>
            <?php endif; ?>
            <p>
                <b><?= INSTALL_INFO_DOCUMENT_ROOT ?></b> <?= $_SERVER['DOCUMENT_ROOT'] ?><br>
                <b><?= INSTALL_INFO_HOST ?></b><?= php_uname('n') ?><br>
                <b><?= INSTALL_INFO_IP ?></b><?= $_SERVER['SERVER_ADDR'] ?>:<?= $_SERVER['SERVER_PORT'] ?>
            </p>
            <?php if (Utils::getEnv()->getValue("installStep") > 1): ?>
                <p>
                    <b><?= INSTALL_GAME ?>:</b> <?= ucfirst(getenv("GAME")) ?>
                </p>
            <?php endif; ?>

        </div>
    </div>
</div>