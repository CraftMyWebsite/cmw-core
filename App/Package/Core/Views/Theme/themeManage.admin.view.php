<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Theme\ThemeManager;
use CMW\Utils\Website;

Website::setTitle(LangManager::translate("core.Theme.manage.title", ["Theme" => ThemeManager::getInstance()->getCurrentTheme()->name()]));
Website::setDescription(LangManager::translate("core.Theme.manage.description")); ?>

<div class="d-flex flex-wrap justify-content-between" style="width: 100%">
    <h3><i class="fa-solid fa-pen-nib"></i> <span
            class="m-lg-auto"><?= LangManager::translate("core.Theme.appearance") ?><b><?= ThemeManager::getInstance()->getCurrentTheme()->name() ?></b></span>
    </h3>
    <div class="d-flex flex-wrap justify-content-end">
        <a href="market/regenerate" type="submit" class="btn btn-warning">
            <?= LangManager::translate("core.Theme.reset") ?>
        </a>
        <div>
            <button form="ThemeSettings" type="submit" class="btn btn-primary">
                <?= LangManager::translate("core.btn.save") ?>
            </button>
        </div>
    </div>
</div>

<!-- THEME CONFIG FILE -->

<form id="ThemeSettings" action="" method="post" enctype="multipart/form-data">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <?php ThemeManager::getInstance()->getCurrentThemeConfigFile(); ?>
            </div>
        </div>
    </div>
</form>