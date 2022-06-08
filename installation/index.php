<!DOCTYPE html>
<?php

use INSTALLTION\GamesController\games;

session_start();
require_once("required.php");
require_once("../app/envBuilder.php");
if (file_exists("../.env")) {
    (new Env("../.env"))->load();
}

$lang = "fr";
if (file_exists("../.env")) :
    $lang = getenv("LOCALE");
else :
    if(isset($_GET['lang'])) :
        $lang = $_GET['lang'];
    endif;
endif;

require_once("lang/$lang.php");
require_once("../app/package/core/lang/$lang.php");

require_once("resources/functions/games.php")
?>
<html lang="<?=$lang?>">
<head>
    <meta charset="utf-8"/>
    <title><?=INSTALL_TITLE?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="robots" content="NOINDEX, NOFOLLOW">
    <meta name="description" content="<?=INSTALL_DESC?>">

    <link rel="icon" type="image/png" href="<?=getenv("PATH_SUBFOLDER")?>admin/resources/images/identity/logo_compact.png">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../admin/resources/vendors/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../admin/resources/css/adminlte.min.css">

    <link rel="stylesheet" href="../admin/resources/css/main.css">

    <link rel="stylesheet" href="resources/css/style.css">

    <script src="../admin/resources/vendors/jquery/jquery.min.js"></script>

    <script src="../admin/resources/js/main.js"></script>

    <style>
        code.dark {
            background: #2b2929;
            padding: 0 5px;
            border-radius: 3px;
        }
    </style>

</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light" id="navbarTop">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>

        <ul class="navbar-nav ml-auto">

            <!-- DARKMODE -->
            <li class="nav-item">
                <a class="nav-link" href="#" id="darkModeToggle">
                    <i class="far fa-lightbulb" id="darkModeIcon"></i>
                </a>
            </li>

        </ul>
    </nav>

    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a class="brand-link">
            <img src="../admin/resources/images/identity/logo_compact.png" alt="<?= CORE_ALT_LOGO ?>"
                 class="brand-image img-circle elevation-3">
            <span class="brand-text font-weight-light">Craft My Website</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <div class="row mb-3 justify-content-center">
                    <div class="col-3">
                        <a href="./?lang=fr"><img src="../admin/resources/vendors/flag-icon-css/flags/fr.svg" class="flag-icon" alt="Passer le site en Français"></a>
                    </div>
                    <div class="col-3">
                        <a href="./?lang=en"><img src="../admin/resources/vendors/flag-icon-css/flags/gb.svg" class="flag-icon" alt="Switch the site to English"></a>
                    </div>
                </div>
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <?php if (!file_exists("../.env") || (isset($_GET['finish_step']) && $_GET['step'] == 5)) : ?>
                        <li class="nav-item">
                            <a class="nav-link <?= (!isset($_GET['step']) || $_GET['step'] == 1) ? "active" : ""; ?>">
                                <i class="nav-icon fas fa-hourglass-start"></i>
                                <p><?=INSTALL_STEP?> 1</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (isset($_GET['step']) && $_GET['step'] == 2) ? "active" : ""; ?>">
                                <i class="nav-icon fas fa-hourglass-half"></i>
                                <p><?=INSTALL_STEP?> 2</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (isset($_GET['step']) && $_GET['step'] == 3) ? "active" : ""; ?>">
                                <i class="nav-icon fas fa-hourglass-end"></i>
                                <p><?=INSTALL_STEP?> 3</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (isset($_GET['step']) && $_GET['step'] == 4) ? "active" : ""; ?>">
                                <i class="nav-icon fas fa-hourglass-end"></i>
                                <p><?=INSTALL_STEP?> 4</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= (isset($_GET['step']) && $_GET['step'] == 5) ? "active" : ""; ?>">
                                <i class="nav-icon fas fa-hourglass-end"></i>
                                <p><?=INSTALL_STEP?> 5</p>
                            </a>
                        </li>

                    <?php else : ?>
                        <li class="nav-item">
                            <a class="nav-link active">
                                <i class="nav-icon fas fa-exclamation-triangle"></i>
                                <p><?=INSTALL_ERROR?></p>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?=INSTALL_MAIN_TITLE?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?=getenv("PATH_SUBFOLDER")?>/cmw-admin">CraftMyWebsite</a></li>
                            <li class="breadcrumb-item active"><?=INSTALL_MAIN_TITLE?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-7">
                    <?php if (!file_exists("../.env") || getenv("STATUS") == 0 || (isset($_GET['finish_step']) && $_GET['step'] == 4)) : ?>
                    <!-- DATABASE INSTALLATION -->
                        <?php if (!isset($_GET['step']) || $_GET['step'] == 1) : ?>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><?=INSTALL_BDD_TITLE?></h3>
                                </div>
                                <!-- form start -->
                                <form action="controller.php" role="form" method="post">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="bdd_name"><?=INSTALL_BDD_NAME?></label>
                                            <input type="text" name="bdd_name" class="form-control" id="bdd_name" required>
                                            <small class="text-muted"><?=INSTALL_BDD_NAME_ABOUT?></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="bdd_login"><?=INSTALL_BDD_USER?></label>
                                            <input type="text" name="bdd_login" class="form-control" id="bdd_login"
                                                   required>
                                            <small class="text-muted"><?=INSTALL_BDD_USER_ABOUT?></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="bdd_pass"><?=INSTALL_BDD_PASS?></label>
                                            <div class="input-group" id="showHidePassword">
                                                <input type="password" name="bdd_pass" class="form-control" id="bdd_pass">
                                                <div class="input-group-append">
                                                    <a class="input-group-text" href="#"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                                </div>
                                            </div>
                                            <small class="text-muted"><?=INSTALL_BDD_PASS_ABOUT?></small>
                                        </div>
                                        <div class="form-group">
                                            <label for="bdd_address"><?=INSTALL_BDD_ADDRESS?></label>
                                            <input type="text" name="bdd_address" class="form-control" id="bdd_address"
                                                   required value="localhost">
                                            <small class="text-muted"><?=INSTALL_BDD_ADDRESS_ABOUT?></small>
                                        </div>
                                        <hr>
                                        <h2><?=INSTALL_SITE_TITLE?></h2>
                                        <div class="form-group">
                                            <label for="install_folder"><?=INSTALL_SITE_FOLDER?></label>
                                            <input type="text" name="install_folder" class="form-control"
                                                   id="install_folder" required value="/">
                                            <small class="text-muted"><?=INSTALL_SITE_FOLDER_ABOUT?></small>
                                        </div>
                                        <div class="form-check">
                                            <input type="checkbox" name="dev_mode" class="form-check-input" id="dev_mode">
                                            <label class="form-check-label" for="dev_mode"><?=INSTALL_DEVMODE_NAME?></label>
                                            <br>
                                            <small class="text-muted"><?=INSTALL_DEVMODE_NAME_ABOUT?></small>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <input type="hidden" name="lang" id="lang" value="<?=$_GET['lang'] ?? 'fr'?>">
                                        <button type="submit" name="update_env" class="btn btn-primary"><?=INSTALL_SAVE?></button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                    <!-- GAME SELECTION -->

                        <?php if (isset($_GET['step']) && $_GET['step'] == 2) : ?>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Selection du jeux</h3>
                                </div>
                                <!-- form start -->
                                <form action="controller.php" role="form" method="post">
                                    <div class="card-body">

                                        <div class="row">

                                            <?php foreach (games::$availableGames as $game):?>
                                            <div class="game-container">

                                                <label class="game-label">
                                                    <input type="radio" name="game" value="<?= $game ?>">

                                                    <img src='resources/img/<?= $game ?>-logo.png'
                                                         class="rounded mx-auto"
                                                         style="max-height: 150px; max-width: 150px"
                                                         alt="Logo <?= $game ?>">
                                                </label>

                                                <span class="game-name"><?= $game ?></span>

                                            </div>
                                            <?php endforeach; ?>

                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" name="create_admin" class="btn btn-primary"><?=INSTALL_SAVE?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                    <!-- ADMIN ACCOUNT CREATION -->

                        <?php if (isset($_GET['step']) && $_GET['step'] == 3) : ?>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><?=INSTALL_ADMIN_TITLE?></h3>
                                </div>
                                <!-- form start -->
                                <form action="controller.php" role="form" method="post">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="email"><?=INSTALL_ADMIN_EMAIL?></label>
                                            <input type="email" name="email" class="form-control" id="email" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="pseudo"><?=INSTALL_ADMIN_USERNAME?></label>
                                            <input type="text" name="pseudo" class="form-control" id="pseudo" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="password"><?=INSTALL_ADMIN_PASS?></label>
                                                <div class="input-group" id="showHidePassword">
                                                    <input type="password" name="password" class="form-control" id="password">
                                                    <div class="input-group-append">
                                                        <a class="input-group-text" href="#"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                                    </div>
                                                </div>
                                        </div>

                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" name="create_admin" class="btn btn-primary"><?=INSTALL_SAVE?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- WEBSITE CONFIGURATION -->

                        <?php if (isset($_GET['step']) && $_GET['step'] == 4) : ?>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><?=INSTALL_CONFIG_TITLE?></h3>
                                </div>
                                <!-- form start -->
                                <form action="controller.php" role="form" method="post">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="config_name"><?=INSTALL_CONFIG_NAME?></label>
                                            <input type="text" name="config_name" class="form-control" id="config_name" maxlength="255" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="config_description"><?=INSTALL_CONFIG_DESCRIPTION?></label>
                                            <input type="text" name="config_description" class="form-control" id="config_description" maxlength="255" required>
                                        </div>

                                        <?php //Minecraft config section
                                            if(getenv("GAME") === "Minecraft"):?>

                                                <div class="form-group">
                                                    <label for="config_minecraft_ip"><?=INSTALL_CONFIG_IP?></label>
                                                    <input type="text" name="config_minecraft_ip" class="form-control" id="config_minecraft_ip" maxlength="255" required>
                                                </div>

                                            <?php endif; ?>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" name="website_config" class="btn btn-primary"><?=INSTALL_SAVE?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>

                    <!-- FINAL SCREEN INSTALLATION COMPLETE -->

                        <?php if (isset($_GET['step']) && $_GET['step'] == 5 && isset($_GET['finish_step'])) : ?>
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title"><?=INSTALL_SUCCESS?> !</h3>
                                </div>
                                <div class="card-body">
                                    <p><?=INSTALL_THANKS?></p>
                                    <hr>
                                    <div class="info-box bg-danger">
                                        <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>

                                        <div class="info-box-content">
                                            <span class="info-box-text font-weight-bold"><?=INSTALL_WARNING_TITLE?></span>
                                            <span class="progress-description"><?=INSTALL_WARNING_FOLDER?></span>
                                        </div>
                                        <!-- /.info-box-content -->
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <a href="../" class="btn btn-primary"><?=INSTALL_LOCATION?></a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else : ?>
                        <div class="alert alert-danger alert-dismissible">
                            <h5><i class="icon fas fa-ban"></i> <?=INSTALL_WARNING_TITLE?> !</h5>
                            <?=INSTALL_WARNING_ENV?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-5">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?=INSTALL_INFOS_TITLE?></h3>
                        </div>
                        <div class="card-body">
                            <p><?= '<b>'.INSTALL_PHP_VERSION_INFOS.' PHP :</b> ' . phpversion();?></p>
                            <?php if (PHP_VERSION_ID < 70400) : ?>
                                <div class="alert alert-danger alert-dismissible">
                                    <p class="info-box-text font-weight-bold"><i class="fas fa-exclamation-triangle"></i> <?=INSTALL_ALERT_VERSION_TITLE?></p>
                                    <?=INSTALL_ALERT_VERSION_INFOS?>
                                </div>
                            <?php endif; ?>
                            <?php if (file_exists("../.env")) : ?>
                                <div class="info-box bg-success">
                                    <span class="info-box-icon"><i class="fas fa-server"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text font-weight-bold"><?=INSTALL_INFOS_SUCESS?></span>
                                        <span class="progress-description"><?=INSTALL_INFOS_TEXT?></span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>

                                <?php $dbHost = getenv("DB_HOST") ?? "<span class='text-danger'>".INSTALL_INFOS_ERROR."</span>";
                                $dbUsername = getenv("DB_USERNAME") ?? "<span class='text-danger'>".INSTALL_INFOS_ERROR."</span>";
                                $dbPassword = !empty(getenv("DB_PASSWORD")) ? "*****" : "<span class='text-danger'>".INSTALL_INFOS_ERROR." / ".INSTALL_INFOS_EMPTY."</span>";
                                $dbName = getenv("DB_NAME") ?? "<span class='text-danger'>".INSTALL_INFOS_ERROR."</span>"; ?>
                                <p>
                                    <b><?=INSTALL_BDD_ADDRESS?> :</b> <?= $dbHost ?><br>
                                    <b><?=INSTALL_BDD_USER_ABOUT?> :</b> <?= $dbUsername ?><br>
                                    <b><?=INSTALL_BDD_PASS_ABOUT?> :</b> <?= $dbPassword ?><br>
                                    <b><?=INSTALL_BDD_NAME?> :</b> <?= $dbName ?>
                                </p>
                                <p>
                                    <b><?= INSTALL_INFO_DOCUMENT_ROOT ?></b> <?= $_SERVER['DOCUMENT_ROOT']; ?><br>
                                    <b><?= INSTALL_INFO_HOST ?></b><?= php_uname('n'); ?><br>
                                    <b><?= INSTALL_INFO_IP ?></b><?= $_SERVER['SERVER_ADDR']; ?>:<?= $_SERVER['SERVER_PORT']; ?>
                                </p>
                                <?php if($_GET['step'] > 2):?>
                                    <p>
                                        <b><?= INSTALL_GAME ?>:</b> <?= getenv("GAME") ?>
                                    </p>
                                <?php endif;?>
                            <?php endif ?>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include_once("../admin/resources/views/includes/footer.inc.php") ?>
</div>

<!-- Bootstrap 4 -->
<script src="../admin/resources/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../admin/resources/js/adminlte.min.js"></script>
<!-- DARKMODE -->
<script src="../admin/resources/js/darkmode.js"></script>
</body>
</html>