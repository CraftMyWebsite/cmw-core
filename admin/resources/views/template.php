<!DOCTYPE html>
<html lang="fr-FR">
<?php

use CMW\Utils\View;

include_once("includes/head.inc.php");

/* INCLUDE SCRIPTS / STYLES*/
View::loadInclude($includes, "beforeScript", "styles");

if (!isset($noBody) || !$noBody) :
?>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <?php include_once("includes/header.inc.php"); ?>
    <?php include_once("includes/sidebar.inc.php"); ?>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?= /** @var string $title */
                            $title ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a
                                        href="<?= getenv("PATH_SUBFOLDER") ?>cmw-admin">CraftMyWebsite</a></li>
                            <li class="breadcrumb-item active"><?= $title ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <?= /** @var string $content */
        $content ?>
    </div>
    <?php include_once("includes/sidebar_cmw.inc.php"); ?>
    <?php include_once("includes/footer.inc.php"); ?>
</div>
<?php else : ?>
    <?= /** @var string $content */
    $content ?>
<?php endif; ?>

<!-- jQuery -->
<!-- Bootstrap 4 -->
<script src="<?= getenv("PATH_SUBFOLDER") ?>admin/resources/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= getenv("PATH_SUBFOLDER") ?>admin/resources/js/adminlte.min.js"></script>
<!-- Darkmode -->
<script src="<?= getenv("PATH_SUBFOLDER") ?>admin/resources/js/darkmode.js"></script>

<?php
/* INCLUDE SCRIPTS */
View::loadInclude($includes, "afterScript");
?>


<?= (isset($scripts) && !empty($scripts)) ? $scripts : "" ?>
<?= (isset($toaster) && !empty($toaster)) ? $toaster : "" ?>
</body>
</html>