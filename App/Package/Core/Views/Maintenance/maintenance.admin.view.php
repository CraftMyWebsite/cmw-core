<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\Core\MaintenanceEntity $maintenance */

$title = LangManager::translate("core.maintenance.title");
$description = LangManager::translate("core.maintenance.description");

?>
<form id="Configuration" action="" method="post" enctype="multipart/form-data">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <div class="d-flex flex-wrap justify-content-between">
        <div class=" form-check-reverse form-switch">
            <label><h4><i class="fa-solid fa-helmet-safety"></i> <span
                            class="m-lg-auto"><?= LangManager::translate("core.maintenance.title") ?></span></h4>
            </label>
            <input class="form-check-input" type="checkbox" id="isEnable" name="isEnable"
                   value="1" <?= $maintenance->isEnable() ? 'checked' : '' ?>>
        </div>
        <div class="buttons">
            <button form="Configuration" type="submit"
                    class="btn btn-primary"><?= LangManager::translate("core.btn.save") ?></button>
        </div>
    </div>
    <section id="mainCard" style="display: <?= $maintenance->isEnable() ? 'block' : 'none' ?>;">
        <div class="card">
            <div class="card-body">
                <div>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><?= LangManager::translate('core.maintenance.settings.targetDateTitle') ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="datetime-local" id="target-date" class="form-control" name="target-date"
                                       value="<?= $maintenance->getTargetDate() ?>"
                                       placeholder="contact@monsite.fr" required>
                                <div class="form-control-icon">
                                    <i class="fa-regular fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate('core.maintenance.settings.loginRegister.title') ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <select class="form-select" name="type" required>
                                    <option value="0" <?= $maintenance->getType() === 0 ? 'selected' : '' ?>>
                                        <?= LangManager::translate('core.maintenance.settings.loginRegister.type.0') ?>
                                    </option>
                                    <option value="1" <?= $maintenance->getType() === 1 ? 'selected' : '' ?>>
                                        <?= LangManager::translate('core.maintenance.settings.loginRegister.type.1') ?>
                                    </option>
                                    <option value="2" <?= $maintenance->getType() === 2 ? 'selected' : '' ?>>
                                        <?= LangManager::translate('core.maintenance.settings.loginRegister.type.2') ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate('core.maintenance.settings.maintenanceTitle.label') ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" name="title" class="form-control"
                                       value="<?= $maintenance->getTitle() ?>" maxlength="255"
                                       placeholder="<?= LangManager::translate('core.maintenance.settings.maintenanceTitle.label') ?>"
                                       required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-heading"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate('core.maintenance.settings.maintenanceDescription.label') ?>
                                :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" name="description" class="form-control"
                                       value="<?= $maintenance->getDescription() ?>" maxlength="255"
                                       placeholder="<?= LangManager::translate('core.maintenance.settings.maintenanceDescription.placeholder') ?>"
                                       required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-circle-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <div class="d-flex flex-wrap justify-content-between">
                    <div class="form-check-reverse form-switch">
                        <label class="form-check-label" for="isOverrideTheme"><?= LangManager::translate('core.maintenance.settings.useMyCode') ?></label>
                        <input class="form-check-input" type="checkbox" id="isOverrideTheme" name="isOverrideTheme"
                               value="1" <?= $maintenance->isOverrideTheme() ? 'checked' : '' ?>>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div style="height: 40vh; display: <?= $maintenance->isOverrideTheme() ? 'block' : 'none' ?>;"
                     id="editor">

                    <div>
                        <?= htmlspecialchars($maintenance->getOverrideThemeCode()) ?>
                    </div>

                </div>
            </div>
        </div>
        <input type="hidden" name="overrideThemeCode" id="overrideThemeCode"
               value="<?= htmlspecialchars($maintenance->getOverrideThemeCode()) ?>">
    </section>
</form>


<!-- Set default dateTarget value if we don't set any target value -->
<script>
    let targetDateIsNull = <?= $maintenance->getTargetDate() === null ? 'true' : 'false'?>;
    if (targetDateIsNull) {
        window.addEventListener("load", function () {
            const now = new Date();
            const offset = now.getTimezoneOffset() * 60000;
            const adjustedDate = new Date(now.getTime() - offset);
            const formattedDate = adjustedDate.toISOString().substring(0, 16); // For minute precision
            const datetimeField = document.getElementById("target-date");
            datetimeField.value = formattedDate;
        });
    }
</script>

<!-- Display card when enable -->
<script>
    const checkbox = document.getElementById('isEnable')
    checkbox.addEventListener('click', function () {
        const mainCard = document.getElementById('mainCard')

        if (checkbox.checked) {
            mainCard.style.display = 'block'
        } else {
            mainCard.style.display = 'none'
        }
    })
</script>

<!-- Display editor when enable -->
<script>
    const checkboxCustomCode = document.getElementById('isOverrideTheme')
    checkboxCustomCode.addEventListener('click', function () {
        const editorCard = document.getElementById('editor')

        if (checkboxCustomCode.checked) {
            editorCard.style.display = 'block'
        } else {
            editorCard.style.display = 'none'
        }
    })
</script>

<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Ace/Src/ace.js' ?>"></script>
<script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Ace/Src/ext-language_tools.js' ?>"></script>
<script>
    let langTools = ace.require("ace/ext/language_tools");
    let editor = ace.edit("editor", {
        mode: "ace/mode/php",
        selectionStyle: "text",
    });

    editor.setOptions({
        autoScrollEditorIntoView: true,
        enableBasicAutocompletion: true,
        enableLiveAutocompletion: true,
        enableSnippets: false
    })

    if (localStorage.getItem('theme') === 'theme-dark') {
        editor.setTheme("ace/theme/cmw_dark");
    } else {
        editor.setTheme("ace/theme/cmw_light");
    }

    editor.resize()
    editor.session.setUseWrapMode(true);
    editor.setShowPrintMargin(false);

    editor.session.mergeUndoDeltas = true;

    const defaultCompletions = [
        "$title",
        "$description",
        "$maintenance"
    ]

    const maintenanceEntity = [
        "$maintenance->isEnable()",
        "$maintenance->getTitle()",
        "$maintenance->getDescription()",
        "$maintenance->getType()",
        "$maintenance->getTargetDate()",
        "$maintenance->getTargetDateFormatted()",
        "$maintenance->getLastUpdateDate()",
        "$maintenance->getLastUpdateDateFormatted()",
    ]

    let myCompleter = {
        identifierRegexps: [/\S+/],
        getCompletions: function (editor, session, pos, prefix, callback) {
            callback(
                null,
                defaultCompletions.filter(entry => {
                    return entry.includes(prefix);
                }).map(entry => {
                    return {
                        value: entry,
                        meta: "local"
                    };
                })
            );
            if (prefix.startsWith("$maintenance")) {
                callback(
                    null,
                    maintenanceEntity.filter(entry => {
                        return entry.includes(prefix);
                    }).map(entry => {
                        return {
                            value: entry,
                            meta: "string"
                        };
                    })
                );
            }
        }
    }

    langTools.addCompleter(myCompleter);


    // Add data to hidden input
    const editorDom = document.getElementById('editor')
    const editorInput = document.getElementById('overrideThemeCode')

    editorDom.addEventListener('keyup', function () {
        editorInput.value = editor.getValue()
    })

</script>