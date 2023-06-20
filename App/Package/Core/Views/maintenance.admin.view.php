<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var \CMW\Entity\Core\MaintenanceEntity $maintenance */

$title = LangManager::translate("core.maintenance.title");
$description = LangManager::translate("core.maintenance.description");

?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-gears"></i>
        <span class="m-lg-auto">
            <?= LangManager::translate("core.maintenance.settings.title") ?>
        </span>
    </h3>
    <div class="buttons">
        <button form="Configuration" type="submit"
                class="btn btn-primary"><?= LangManager::translate("core.btn.save", lineBreak: true) ?></button>
    </div>
</div>
<section class="row">
    <div class="col-12">
        <div class="card">
            <form id="Configuration" action="" method="post" enctype="multipart/form-data">
                <?php (new SecurityManager())->insertHiddenToken() ?>
                <div class="card-header">
                    <h4><?= LangManager::translate("core.config.title") ?></h4>
                    <div class="form-check-reverse form-switch align-right">
                        <label class="form-check-label"
                               for="isEnable"><?= LangManager::translate('core.btn.enable') ?></label>
                        <input class="form-check-input" type="checkbox" id="isEnable" name="isEnable"
                               value="1" <?= $maintenance->isEnable() ? 'checked' : '' ?>>
                    </div>
                </div>
                <div class="card-body" id="mainCard"
                     style="display: <?= $maintenance->isEnable() ? 'block' : 'none' ?>;">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><?= LangManager::translate('core.maintenance.settings.targetDateTitle') ?> :</h6>
                            <div class="form-group">
                                <input type="datetime-local" id="target-date"
                                       value="<?= $maintenance->getTargetDate() ?>"
                                       name="target-date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><?= LangManager::translate('core.maintenance.settings.loginRegister.title') ?> :</h6>
                            <div class="form-group">
                                <select class="choices form-select" name="type" required>
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
                    <div class="row">
                        <div class="col-md-6">
                            <h6><?= LangManager::translate('core.maintenance.settings.maintenanceTitle.label') ?> :</h6>
                            <div class="form-group position-relative has-icon-left">
                                <input type="text" name="title" class="form-control" maxlength="255"
                                       value="<?= $maintenance->getTitle() ?>"
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
                                       value="<?= $maintenance->getDescription() ?>"
                                       placeholder="<?= LangManager::translate('core.maintenance.settings.maintenanceDescription.placeholder') ?>"
                                       required>
                                <div class="form-control-icon">
                                    <i class="fa-solid fa-circle-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Set default dateTarget value if we don't set any target value -->
<script>
    if (<?= $maintenance->getTargetDate() === null ? 'true' : false ?>) {
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