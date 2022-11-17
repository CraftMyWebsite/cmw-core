<?php use CMW\Controller\Core\CoreController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\CoreModel;
use CMW\Utils\SecurityService;
use CMW\Utils\Utils;

$title = LangManager::translate("core.config.title");
$description = LangManager::translate("core.config.desc"); 
?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gears"></i> <span class="m-lg-auto">Réglages</span></h3>
    <div class="buttons"><button form="Configuration" type="submit" class="btn btn-primary"><?= LangManager::translate("core.btn.save", lineBreak: true) ?></button></div>
</div>
<section class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4><?= LangManager::translate("core.config.title") ?></h4>
            </div>
            <div class="card-body">
                <form id="Configuration" action="" method="post" enctype="multipart/form-data">
                    <?php (new SecurityService())->insertHiddenToken() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("core.website.name") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" name="name" class="form-control" value="<?= CoreModel::getOptionValue("name") ?>"
                                               placeholder="<?= LangManager::translate("core.website.name") ?>" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-signature"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("core.website.description") ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" name="description" class="form-control"
                                       value="<?= CoreModel::getOptionValue("description") ?>"
                                               placeholder="<?= LangManager::translate("core.website.description") ?>" required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-circle-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("core.lang.change") ?> :</h6>
                            <div class="form-group">
                                <select class="choices form-select" name="locale">
                                    <?php foreach (CoreController::$availableLocales as $code => $name): ?>
                                    <option value="<?= $code ?>" <?= $code === getenv("LOCALE") ? "selected" : "" ?>>
                                    <?= $name ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate("core.config.favicon") ?> :</h6>
                            <input class="form-control form-control-lg" type="file" id="favicon" accept="image/x-icon" name="favicon">
                            <small><?= LangManager::translate("core.config.favicon_tips") ?></small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>



<!--EXTENSION select choice-->
<script src="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>admin/resources/vendors/choices.js/public/assets/scripts/choices.js"></script>
<script src="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>admin/resources/assets/js/pages/form-element-select.js"></script>