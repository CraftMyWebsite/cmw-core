<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\Core\MaintenanceEntity $maintenance */

$title = LangManager::translate('core.maintenance.title');
$description = LangManager::translate('core.maintenance.description');

?>

<div class="page-title">
    <h3><i class="fa-solid fa-helmet-safety"></i> <?= LangManager::translate('core.maintenance.title') ?> <?= $maintenance->isEnable() ? ' - <i class="fa-solid fa-check text-success"></i> '. LangManager::translate('core.maintenance.settings.activated') : ' - <i class="fa-solid fa-xmark text-danger"></i> '. LangManager::translate('core.maintenance.settings.deactivated') ?></h3>
    <button form="Configuration" class="btn-primary"><?= LangManager::translate('core.btn.save') ?></button>
</div>

<form id="Configuration" action="" method="post" enctype="multipart/form-data">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
    <label class="toggle">
        <input type="checkbox" id="isEnable" name="isEnable" <?= $maintenance->isEnable() ? 'checked' : '' ?>
               class="toggle-input">
        <div class="toggle-slider"></div>
        <h5 class="toggle-label"><?= LangManager::translate('core.maintenance.main_label') ?></h5>
    </label>

    <section class="mt-4" id="mainCard" style="display: <?= $maintenance->isEnable() ? 'block' : 'none' ?>;">
        <div class="grid-2">
            <div class="card">
                <h6><?= LangManager::translate('core.maintenance.settings.info') ?></h6>
                <div>
                    <label for="title"><?= LangManager::translate('core.maintenance.settings.maintenanceTitle.label') ?> :</label>
                    <div class="input-group">
                        <i class="fa-solid fa-heading"></i>
                        <input type="text" id="title" name="title"
                               value="<?= $maintenance->getTitle() ?>" maxlength="255"
                               placeholder="<?= LangManager::translate('core.maintenance.settings.maintenanceTitle.label') ?>"
                               required>
                    </div>
                </div>
            </div>
            <div class="card">
                <h6><?= LangManager::translate('core.maintenance.settings.title') ?></h6>
                <label class="toggle">
                    <p class="toggle-label"><?= LangManager::translate('core.maintenance.settings.noEnd') ?></p>
                    <input type="checkbox" id="noEnd" name="noEnd" <?= $maintenance->noEnd() ? 'checked' : '' ?>
                           class="toggle-input">
                    <div class="toggle-slider"></div>
                </label>
                    <div id="Date">
                        <label for="target-date"><?= LangManager::translate('core.maintenance.settings.targetDateTitle') ?> :</label>
                        <div class="input-group">
                            <i class="fa-regular fa-clock"></i>
                            <input type="datetime-local" id="target-date" name="target-date"
                                   value="<?= $maintenance->getTargetDate() ?>" required>
                        </div>
                    </div>
                    <div>
                        <label for="type"><?= LangManager::translate('core.maintenance.settings.loginRegister.title') ?> :</label>
                        <select id="type" name="type" required>
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
        </div>

            <div class="card mt-4">
                <label class="toggle">
                    <p class="toggle-label">Ajouter une description</p>
                    <input type="checkbox" id="isOverrideTheme" name="isOverrideTheme"
                           value="1" <?= $maintenance->isOverrideTheme() ? 'checked' : '' ?>
                           class="toggle-input">
                    <div class="toggle-slider"></div>
                    <p class="toggle-label"><?= LangManager::translate('core.maintenance.settings.useMyCode') ?></p>
                </label>
                <div id="desc">
                    <textarea name="description" id="description" class="tinymce"><?= $maintenance->getDescription() ?></textarea>
                </div>
                <div style="height: 40vh; display: <?= $maintenance->isOverrideTheme() ? 'block' : 'none' ?>;" id="editor">
                    <div>
                        <?= htmlspecialchars($maintenance->getOverrideThemeCode() ?? ' ') ?>
                    </div>
                </div>
                <input type="hidden" name="overrideThemeCode" id="overrideThemeCode"
                       value="<?= htmlspecialchars($maintenance->getOverrideThemeCode()) ?>">
            </div>

    </section>
</form>


<!-- Set default dateTarget value if we don't set any target value -->
<script>
    let targetDateIsNull = <?= $maintenance->getTargetDate() === null ? 'true' : 'false' ?>;
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
    document.addEventListener('DOMContentLoaded', function () {
        const checkboxCustomCode = document.getElementById('isOverrideTheme')
        const editorCard = document.getElementById('editor')
        const descCard = document.getElementById('desc')
        function updateDisplay() {
            if (checkboxCustomCode.checked) {
                editorCard.style.display = 'block'
                descCard.style.display = 'none'
            } else {
                editorCard.style.display = 'none'
                descCard.style.display = 'block'
            }
        }
        updateDisplay()
        checkboxCustomCode.addEventListener('click', updateDisplay)
    })
</script>

<script
    src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Ace/Src/ace.js' ?>"></script>
<script
    src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Vendors/Ace/Src/ext-language_tools.js' ?>"></script>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox = document.getElementById('noEnd');
        const targetDateDiv = document.querySelector('#Date').closest('div');
        function toggleTargetDateDisplay() {
            if (checkbox.checked) {
                targetDateDiv.style.display = 'block';
            } else {
                targetDateDiv.style.display = 'none';
            }
        }

        toggleTargetDateDisplay();
        checkbox.addEventListener('change', toggleTargetDateDisplay);
    });
</script>