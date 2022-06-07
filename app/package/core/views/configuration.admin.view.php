<?php use CMW\Model\coreModel;

$title = CORE_CONFIG_TITLE;
$description = CORE_CONFIG_DESC; ?>

<?php ob_start(); ?>
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="" method="post">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><?=USERS_ADD_CARD_TITLE?> :</h3>
                            </div>
                            <div class="card-body">

                                <!-- GENERAL CONFIG SECTION -->

                                <div class="form-group">
                                    <label for="name"><?= CORE_WEBSITE_NAME ?></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                        </div>
                                        <input type="text" name="name" class="form-control" value="<?= coreModel::getOptionValue("name") ?>"
                                               placeholder="<?=CORE_WEBSITE_NAME?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description"><?= CORE_WEBSITE_DESCRIPTION ?></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-paragraph"></i></span>
                                        </div>
                                        <input type="text" name="description" class="form-control" value="<?= coreModel::getOptionValue("description") ?>"
                                               placeholder="<?=CORE_WEBSITE_DESCRIPTION?>" required>
                                    </div>
                                </div>

                                <?php //Minecraft config section
                                    if(getenv("GAME") === "Minecraft"):?>
                                    <div class="form-group">
                                        <label for="ip"><?= CORE_MINECRAFT_IP ?></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-network-wired"></i></span>
                                            </div>
                                            <input type="text" name="ip" class="form-control" value="<?= coreModel::getOptionValue("ip") ?>"
                                                   placeholder="<?=CORE_MINECRAFT_IP?>" required>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary float-right"><?=BTN_SAVE?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.row -->
        </div>
    </div>
<?php $content = ob_get_clean(); ?>