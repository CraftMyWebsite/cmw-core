<?php use CMW\Controller\CoreController;
use CMW\Model\CoreModel;

$title = CORE_CONFIG_TITLE;
$description = CORE_CONFIG_DESC; ?>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="" method="post">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><?= CORE_CONFIG_TITLE ?> :</h3>
                            </div>
                            <div class="card-body">

                                <!-- GENERAL CONFIG SECTION -->

                                <div class="form-group">
                                    <label for="name"><?= CORE_WEBSITE_NAME ?></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                        </div>
                                        <input type="text" name="name" class="form-control"
                                               value="<?= CoreModel::getOptionValue("name") ?>"
                                               placeholder="<?= CORE_WEBSITE_NAME ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description"><?= CORE_WEBSITE_DESCRIPTION ?></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-paragraph"></i></span>
                                        </div>
                                        <input type="text" name="description" class="form-control"
                                               value="<?= CoreModel::getOptionValue("description") ?>"
                                               placeholder="<?= CORE_WEBSITE_DESCRIPTION ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><?= CORE_CHANGE_LANG ?></label>
                                    <select class="form-control" name="locale">
                                        <?php foreach (CoreController::$availableLocales as $code => $name): ?>
                                        <option value="<?= $code ?>" <?= $code === getenv("LOCALE") ? "selected" : "" ?>>
                                            <?= $name ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <?php //Minecraft config section
                                if (getenv("GAME") === "minecraft"):?>
                                    <div class="form-group">
                                        <label for="minecraft_ip"><?= CORE_MINECRAFT_IP ?></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                            class="fas fa-network-wired"></i></span>
                                            </div>
                                            <input type="text" name="minecraft_ip" id="minecraft_ip"
                                                   class="form-control"
                                                   value="<?= CoreModel::getOptionValue("minecraft_ip") ?>"
                                                   placeholder="<?= CORE_MINECRAFT_IP ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                            <input type="checkbox" class="custom-control-input"
                                                   name="minecraft_register_premium" id="minecraft_register_premium"
                                                   value="true" <?= CoreModel::getOptionValue("minecraft_register_premium") === "true" ? "checked" : "" ?>>
                                            <label class="custom-control-label" for="minecraft_register_premium">
                                                <?= CORE_MINECRAFT_REGISTER_PREMIUM ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; ?>

                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary float-right"><?= CORE_BTN_SAVE ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.row -->
        </div>
    </div>