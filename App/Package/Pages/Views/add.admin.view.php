<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

$title = LangManager::translate('pages.add.title');
$description = LangManager::translate('pages.add.desc');
?>

<h3><i class="fa-solid fa-file-lines"></i> <?= LangManager::translate('pages.add.title') ?></h3>

<form action="" method="post">
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
    <div class="grid-5">
        <div class="col-span-4">
            <input type="hidden" id="page_id" name="id" value="">
            <textarea id="content" class="tinymce" name="content" data-tiny-height="700"></textarea>
        </div>
        <div class="card space-y-4">
            <div>
                <label for="title"><?= LangManager::translate('pages.title') ?> :</label>
                <input type="text" id="title" name="title" required class="input-sm"
                       placeholder="<?= LangManager::translate('pages.title') ?>" value="">
            </div>
            <div>
                <label for="slug">URL :</label>
                <div class="input-group-sm">
                    <i><?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?></i>
                    <input type="text" value="" id="slug" name="page_slug" required>
                </div>
            </div>
            <label class="toggle">
                <p class="toggle-label"><?= LangManager::translate('pages.draft') ?></p>
                <input type="checkbox" class="toggle-input" name="state">
                <div class="toggle-slider"></div>
            </label>
            <button type="submit" class="btn-primary mt-4 loading-btn btn-center"
                    data-loading-btn="<?= LangManager::translate('core.btn.saving') ?>"><?= LangManager::translate('core.btn.add') ?></button>
        </div>
    </div>
</form>