<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Utils\Website;

$title = LangManager::translate("pages.add.title");
$description = LangManager::translate("pages.add.desc");
?>

<h3><i class="fa-solid fa-file-lines"></i> <?= LangManager::translate("pages.add.title") ?></h3>

<div class="card">
    <form action="" method="post">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <input type="hidden" id="page_id" name="id" value="">
        <div class="grid-2">
            <div>
                <label for="title"><?= LangManager::translate("pages.title") ?> :</label>
                <input type="text" id="title" name="title" required class="input"
                       placeholder="<?= LangManager::translate("pages.title") ?>" value="">
            </div>
            <div>
                <label for="slug"><?= LangManager::translate("pages.title") ?> :</label>
                <div class="input-group">
                    <i><?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?></i>
                    <input type="text" value="" id="slug" name="page_slug" required>
                </div>
            </div>
        </div>
        <div>
            <label class="toggle">
                <p class="toggle-label"><?= LangManager::translate("pages.draft") ?></p>
                <input type="checkbox" class="toggle-input" name="state">
                <div class="toggle-slider"></div>
            </label>
        </div>
        <div class="mt-2">
            <label for="content"><?= LangManager::translate("pages.creation.content") ?> :</label>
            <textarea id="content" class="tinymce" name="content"></textarea>
        </div>
        <button type="submit" class="btn-primary btn-center mt-4 loading-btn" data-loading-btn="Sauvegarde ..."><?= LangManager::translate("core.btn.add") ?></button>
    </form>
</div>