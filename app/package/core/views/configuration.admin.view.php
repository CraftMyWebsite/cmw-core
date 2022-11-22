<?php use CMW\Controller\Core\CoreController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\CoreModel;
use CMW\Utils\SecurityService;

$title = LangManager::translate("core.config.title");
$description = LangManager::translate("core.config.desc");
?>
<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gears"></i> <span class="m-lg-auto">Réglages</span></h3>
    <div class="buttons">
        <button form="Configuration" type="submit"
                class="btn btn-primary"><?= LangManager::translate("core.btn.save", lineBreak: true) ?></button>
    </div>
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
                                <input type="text" name="name" class="form-control"
                                       value="<?= CoreModel::getOptionValue("name") ?>"
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
                                <select class="choices form-select" name="locale" required>
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
                            <input class="form-control form-control-lg" type="file" id="favicon" accept="image/x-icon"
                                   name="favicon">
                            <small><?= LangManager::translate("core.config.favicon_tips") ?></small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6><?= LangManager::translate('core.config.dateFormat') ?> : <i data-bs-toggle="tooltip"
                                                                                             title="<?= LangManager::translate('core.config.dateFormatTooltip') ?>"
                                                                                             class="fa-sharp fa-solid fa-circle-question"></i>
                            </h6>
                            <fieldset class="form-group">
                                <select class="form-select" name="dateFormat" id="dateFormatSelect"
                                        onchange="if(this.options[this.selectedIndex].value === 'custom'){toggleField(this, document.getElementById('dateFormatCustom'));this.selectedIndex='0';}">


                                    <?php foreach (CoreController::$exampleDateFormat as $dateFormat): ?>
                                        <option value="<?= $dateFormat ?>"
                                            <?= CoreModel::getOptionValue("dateFormat") === $dateFormat ? 'selected' : '' ?>>
                                            <?= $dateFormat ?>
                                        </option>
                                    <?php endforeach; ?>

                                    <option value="custom"><?= LangManager::translate('core.config.custom') ?></option>

                                    <input id="dateFormatCustom" class="form-control" name="dateFormat"
                                           style="display:none;"
                                           disabled="disabled"
                                           onblur="if(this.value === ''){toggleField(this, document.getElementById('dateFormatSelect'));}">

                                    <?php if (!in_array(CoreModel::getOptionValue("dateFormat"), CoreController::$exampleDateFormat, true)): ?>
                                        <script>
                                            document.getElementById('dateFormatSelect').style.display = "none";
                                            document.getElementById('dateFormatSelect').disabled = true;
                                        </script>
                                        <input id="dateFormatCustom" class="form-control" name="dateFormat"
                                               value="<?= CoreModel::getOptionValue("dateFormat") ?>"
                                               style="display:inline;"
                                               onblur="if(this.value === ''){toggleField(this, document.getElementById('dateFormatSelect'));}">
                                    <?php endif; ?>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    function toggleField(hideObj, showObj) {
        hideObj.disabled = true;
        hideObj.style.display = 'none';
        showObj.disabled = false;
        showObj.style.display = 'inline';
        showObj.focus();
    }
</script>