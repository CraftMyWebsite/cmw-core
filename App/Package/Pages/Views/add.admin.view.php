<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

$title = LangManager::translate('pages.add.title');
$description = LangManager::translate('pages.add.desc');
?>
<form method='post'>
    <?php SecurityManager::getInstance()->insertHiddenToken() ?>
    <div class="flex page-title">
        <h3><i class="fa-solid fa-file-lines"></i> <?= LangManager::translate('pages.add.title') ?></h3>
        <button type='submit' class='btn-primary loading-btn'
                data-loading-btn="<?= LangManager::translate('core.btn.saving') ?>">
            <?= LangManager::translate('core.btn.save') ?>
        </button>
    </div>

    <div class="mt-4 card">
        <div class='grid-6'>
            <div class="col-span-5">
                <div class="grid-2">
                    <div>
                        <label for='title'><?= LangManager::translate('pages.title') ?> :</label>
                        <input type="text" id="title" name="title" required class="input-sm"
                               placeholder="<?= LangManager::translate('pages.title') ?>" value="">
                    </div>
                    <div>
                        <label for="slug"><?= LangManager::translate('pages.link') ?> :</label>
                        <div class="input-group-sm">
                            <i><?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?></i>
                            <input type="text" value="" id="slug" name="page_slug" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-stretch">
                <label class='toggle'>
                    <p class='toggle-label'><?= LangManager::translate('pages.draft') ?></p>
                    <input type="checkbox" class="toggle-input" name="state">
                    <div class="toggle-slider"></div>
                </label>
            </div>
        </div>
    </div>

    <div class="mt-4 card">
        <h5><?= LangManager::translate('pages.content') ?>:</h5>
        <input type="hidden" id="page_id" name="id" value="">
        <textarea id="content" class="tinymce" name="content" data-tiny-height="700"></textarea>
    </div>
</form>
