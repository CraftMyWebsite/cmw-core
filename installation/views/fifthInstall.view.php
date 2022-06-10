<?php
/* @var \CMW\Controller\Installer\installerController $install */

$install->endInstallation();
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= INSTALL_SUCCESS ?> !</h3>
    </div>
    <div class="card-body">
        <p><?= INSTALL_THANKS ?></p>
        <hr>
        <div class="info-box bg-danger">
            <span class="info-box-icon"><i class="fas fa-exclamation-triangle"></i></span>

            <div class="info-box-content">
                <span class="info-box-text font-weight-bold"><?= INSTALL_WARNING_TITLE ?></span>
                <span class="progress-description"><?= INSTALL_WARNING_FOLDER ?></span>
            </div>
            <!-- /.info-box-content -->
        </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer">
        <a href="<?= getenv("PATH_SUBFOLDER") ?>" class="btn btn-primary"><?= INSTALL_LOCATION ?></a>
    </div>
</div>