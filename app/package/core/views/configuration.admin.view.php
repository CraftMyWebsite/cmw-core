<?php use CMW\Controller\Core\CoreController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\CoreModel;

$title = LangManager::translate("core.config.title");
$description = LangManager::translate("core.config.desc"); ?>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title"><?= LangManager::translate("core.config.title") ?> :</h3>
                            </div>
                            <div class="card-body">

                                <!-- GENERAL CONFIG SECTION -->

                                <div class="form-group">
                                    <label for="name"><?= LangManager::translate("core.website.name") ?></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                        </div>
                                        <input type="text" name="name" class="form-control"
                                               value="<?= CoreModel::getOptionValue("name") ?>"
                                               placeholder="<?= LangManager::translate("core.website.name") ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description"><?= LangManager::translate("core.website.description") ?></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-paragraph"></i></span>
                                        </div>
                                        <input type="text" name="description" class="form-control"
                                               value="<?= CoreModel::getOptionValue("description") ?>"
                                               placeholder="<?= LangManager::translate("core.website.description") ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><?= LangManager::translate("core.lang.change") ?></label>
                                    <select class="form-control" name="locale">
                                        <?php foreach (CoreController::$availableLocales as $code => $name): ?>
                                        <option value="<?= $code ?>" <?= $code === getenv("LOCALE") ? "selected" : "" ?>>
                                            <?= $name ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="favicon" accept="image/x-icon"
                                               name="favicon">
                                        <label class="custom-file-label" for="favicon">
                                            <?= LangManager::translate("core.config.favicon") ?>
                                        </label>
                                    </div>
                                    <small><?= LangManager::translate("core.config.favicon_tips") ?></small>
                                </div>

                                <?php //Minecraft config section
                                /*
                                 * //TODO USE GAMEFABRIC
                                if (getenv("GAME") === "minecraft"):?>
                                    <div class="form-group">
                                        <label for="minecraft_ip"><?= LangManager::translate("core.minecraft.ip", lineBreak: true) ?></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                            class="fas fa-network-wired"></i></span>
                                            </div>
                                            <input type="text" name="minecraft_ip" id="minecraft_ip"
                                                   class="form-control"
                                                   value="<?= CoreModel::getOptionValue("minecraft_ip") ?>"
                                                   placeholder="<?= LangManager::translate("core.minecraft.ip", lineBreak: true) ?>" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                            <input type="checkbox" class="custom-control-input"
                                                   name="minecraft_register_premium" id="minecraft_register_premium"
                                                   value="true" <?= CoreModel::getOptionValue("minecraft_register_premium") === "true" ? "checked" : "" ?>>
                                            <label class="custom-control-label" for="minecraft_register_premium">
                                                <?= LangManager::translate("core.minecraft.register", lineBreak: true) ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endif; */?>

                            </div>
                            <!-- /.card-body -->
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary float-right"><?= LangManager::translate("core.btn.save", lineBreak: true) ?></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.row -->
        </div>
    </div>