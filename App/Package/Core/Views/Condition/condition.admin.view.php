<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate('core.config.title');
$description = LangManager::translate('core.config.desc');

/* @var CMW\Entity\Core\ConditionEntity $cgv */
/* @var CMW\Entity\Core\ConditionEntity $cgu */
?>

<div class="page-title">
    <h3><i class="fa-solid fa-gavel"></i> <?= LangManager::translate('core.condition.title') ?></h3>
    <button form="condition" type="submit" class="btn-primary"><?= LangManager::translate('core.btn.save') ?></button>
</div>

<form id="condition" action="" method="post" enctype="multipart/form-data">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
    <div class="grid-2">

        <div class="card">
            <h6><?= LangManager::translate('core.condition.cgv') ?></h6>
            <div>
                <label class="toggle">
                    <p class="toggle-label"><?= LangManager::translate('core.condition.activecgv') ?></p>
                    <input class="toggle-input" type="checkbox" id="conditionState"
                           name="cgvState" <?= $cgv->isState() ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                </label>
            </div>
            <textarea id="conditionContent" class="tinymce" name="cgvContent"
                      data-tiny-height="600"><?= $cgv->getContent() ?></textarea>
            <p>
                <?= LangManager::translate('core.condition.updateby', ['author' => $cgv->getLastEditor()?->getPseudo()]) ?>
                <?= LangManager::translate('core.condition.on', ['date' => $cgv->getUpdateFormatted()]) ?>
            </p>
        </div>
        <div class="card">
            <h6><?= LangManager::translate('core.condition.cgu') ?></h6>
            <div>
                <label class="toggle">
                    <p class="toggle-label"><?= LangManager::translate('core.condition.activecgu') ?></p>
                    <input class="toggle-input" type="checkbox" id="conditionState"
                           name="cguState" <?= $cgu->isState() ? 'checked' : '' ?>>
                    <div class="toggle-slider"></div>
                </label>
            </div>
            <textarea id="conditionContent2" class="tinymce" name="cguContent"><?= $cgu->getContent() ?></textarea>
            <p>
                <?= LangManager::translate('core.condition.updateby', ['author' => $cgu->getLastEditor()?->getPseudo()]) ?>
                <?= LangManager::translate('core.condition.on', ['date' => $cgu->getUpdateFormatted()]) ?>
            </p>
        </div>
    </div>
</form>