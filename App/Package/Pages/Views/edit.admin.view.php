<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

$title = LangManager::translate('pages.edit.title');
$description = LangManager::translate('pages.edit.desc');

/*
 * @var \CMW\Entity\Pages\PageEntity $page
 */

?>

<h3><i class="fa-solid fa-file-lines"></i> <?= LangManager::translate('pages.edit.title') ?> : <?= $page->getTitle() ?></h3>

<form action="" method="post">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <div class="grid-5">
        <div class="col-span-4">
            <input type="hidden" id="page_id" name="id" value="<?= $page->getId() ?>">
            <textarea id="content" class="tinymce" name="content" data-tiny-height="700"><?= $page->getContent() ?></textarea>
        </div>
        <div class="card space-y-4">
            <div>
                <label for="title"><?= LangManager::translate('pages.title') ?> :</label>
                <input type="text" id="title" name="title" required class="input-sm"
                       placeholder="<?= LangManager::translate('pages.title') ?>" value="<?= $page->getTitle() ?>">
            </div>
            <div>
                <label for="slug">URL :</label>
                <div class="input-group-sm">
                    <i><?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?></i>
                    <input type="text" value="<?= $page->getSlug() ?>" id="slug" name="slug" required disabled>
                </div>
            </div>
            <label class="toggle">
                <p class="toggle-label"><?= LangManager::translate('pages.draft') ?></p>
                <input type="checkbox" class="toggle-input" name="state" <?= $page->getState() === 1 ? 'checked' : '' ?>>
                <div class="toggle-slider"></div>
            </label>
            <button type="submit" class="btn-primary mt-4 loading-btn btn-center"
                    data-loading-btn="Sauvegarde ..."><?= LangManager::translate('core.btn.save') ?></button>
        </div>
    </div>
</form>