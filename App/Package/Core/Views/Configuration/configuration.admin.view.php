<?php
use CMW\Controller\Core\CoreController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Model\Core\CoreModel;

$title = LangManager::translate('core.config.title');
$description = LangManager::translate('core.config.desc');
?>

<div class="page-title mb-4">
    <h3><i class="fa-solid fa-gears"></i> <?= LangManager::translate('core.config.title') ?></h3>
    <button form="Configuration" type="submit"
            class="btn-primary"><?= LangManager::translate('core.btn.save') ?></button>
</div>

<form id="Configuration" action="" method="post" enctype="multipart/form-data">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <div class="center-flex">
        <div class="flex-content-lg space-y-3">
            <div class="card space-y-3">
                <div>
                    <label for="name"><?= LangManager::translate('core.website.name') ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-signature"></i>
                        <input type="text" id="name" name="name" required
                               placeholder="<?= LangManager::translate('core.website.name') ?>"
                               value="<?= CoreModel::getOptionValue('name') ?>">
                    </div>
                </div>
                <div>
                    <label for="description"><?= LangManager::translate('core.website.description') ?> :</label>
                    <textarea id="description" name="description"
                              placeholder="<?= LangManager::translate('core.website.description') ?>"
                              class="textarea"><?= CoreModel::getOptionValue('description') ?></textarea>
                </div>
            </div>
            <div class="card space-y-3">
                <div>
                    <label for="locale"><?= LangManager::translate('core.Lang.change') ?> :</label>
                    <select id="locale" class="choices" name="locale" required>
                        <?php foreach (CoreController::$availableLocales as $code => $name): ?>
                            <option
                                value="<?= $code ?>" <?= $code === EnvManager::getInstance()->getValue('LOCALE') ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="dateFormatSelect"><?= LangManager::translate('core.config.dateFormat') ?> :
                        <button data-tooltip-target="tooltip-top" data-tooltip-placement="top"><i
                                class="fa-sharp fa-solid fa-circle-question"></i></button>
                        <div id="tooltip-top" role="tooltip" class="tooltip-content">
                            <?= LangManager::translate('core.config.dateFormatTooltip') ?>
                        </div>
                    </label>
                    <select name="dateFormat" id="dateFormatSelect"
                            onchange="if(this.options[this.selectedIndex].value === 'custom'){toggleField(this, document.getElementById('dateFormatCustom'));this.selectedIndex='0';}">
                        <?php foreach (CoreController::$exampleDateFormat as $dateFormat): ?>
                            <option value="<?= $dateFormat ?>"
                                <?= CoreModel::getOptionValue('dateFormat') === $dateFormat ? 'selected' : '' ?>>
                                <?= $dateFormat ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="custom"><?= LangManager::translate('core.config.custom') ?></option>
                        <input id="dateFormatCustom" class="input" name="dateFormat"
                               style="display:none;"
                               disabled="disabled"
                               onblur="if(this.value === ''){toggleField(this, document.getElementById('dateFormatSelect'));}">

                        <?php if (!in_array(CoreModel::getOptionValue('dateFormat'), CoreController::$exampleDateFormat, true)): ?>
                            <script>
                                document.getElementById('dateFormatSelect').style.display = "none";
                                document.getElementById('dateFormatSelect').disabled = true;
                            </script>
                            <input id="dateFormatCustom" class="input" name="dateFormat"
                                   value="<?= CoreModel::getOptionValue('dateFormat') ?>"
                                   style="display:inline;"
                                   onblur="if(this.value === ''){toggleField(this, document.getElementById('dateFormatSelect'));}">
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="card">
                <label> <?= LangManager::translate('core.config.favicon') ?> :</label>
                <div class="flex">
                    <small>Actuel :</small>
                    <img width="16px"
                         src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Uploads/Favicon/favicon.ico">
                </div>
                <div class="drop-img-area" data-input-name="favicon" data-img-accept="image/x-icon"></div>
                <p><small>*<?= LangManager::translate('core.config.favicon_tips') ?></small></p>
            </div>

        </div>
    </div>
</form>

<script>
    function toggleField(hideObj, showObj) {
        hideObj.disabled = true;
        hideObj.style.display = 'none';
        showObj.disabled = false;
        showObj.style.display = 'inline';
        showObj.focus();
    }
</script>