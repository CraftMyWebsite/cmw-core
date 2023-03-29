<?php
/* @var \CMW\Controller\Installer\InstallerController $install */
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= INSTALL_CONFIG_TITLE ?></h3>
    </div>
    <!-- form start -->
    <form action="installer/submit" method="post" role="form" id="formFourthInstall" name="formFourthInstall">
        <div class="card-body">
            <div class="form-group">
                <label for="config_name"><?= INSTALL_CONFIG_NAME ?></label>
                <input type="text" name="config_name" class="form-control" id="config_name"
                       placeholder="Hypixel"
                       autocomplete="organization"
                       maxlength="255" required>
            </div>
            <div class="form-group">
                <label for="config_description"><?= INSTALL_CONFIG_DESCRIPTION ?></label>
                <input type="text" name="config_description" class="form-control"
                       placeholder="<?= INSTALL_CONFIG_DESCRIPTION_PLACEHOLDER ?>"
                       id="config_description" maxlength="255" required>
            </div>

            <?php $install->loadHTMLGame() ?>

        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <button type="submit" id="submitFourthInstall" name="submitFourthInstall"
                    class="btn btn-primary"><?= INSTALL_SAVE ?>
            </button>
        </div>
    </form>
</div>