<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Theme\ThemeManager;
use CMW\Utils\Website;

Website::setTitle(LangManager::translate("core.Theme.manage.title", ["Theme" => ThemeManager::getInstance()->getCurrentTheme()->name()]));
Website::setDescription(LangManager::translate("core.Theme.manage.description")); ?>

<div class="page-title">
    <h3><i class="fa-solid fa-palette"></i> <?= LangManager::translate("core.Theme.appearance") ?><b><?= ThemeManager::getInstance()->getCurrentTheme()->name() ?></b></h3>
    <div class="flex gap-2">
        <a href="market/regenerate" type="submit" class="btn-warning">
            <?= LangManager::translate("core.Theme.reset") ?>
        </a>
        <div>
            <button form="ThemeSettings" type="submit" class="btn-primary">
                <?= LangManager::translate("core.btn.save") ?>
            </button>
        </div>
    </div>
</div>

<div class="page-loader">
    <form id="ThemeSettings" action="" method="post" enctype="multipart/form-data">
        <?php (new SecurityManager())->insertHiddenToken() ?>
        <div class="card">
            <?php ThemeManager::getInstance()->getCurrentThemeConfigFile(); ?>
        </div>
    </form>
</div>
