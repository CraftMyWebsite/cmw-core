<?php

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate("core.theme.manage.title", ["theme" => ThemeController::getCurrentTheme()->getName()]);
$description = LangManager::translate("core.theme.manage.description"); ?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-pen-nib"></i> <span class="m-lg-auto">Apparence de <b><?= ThemeController::getCurrentTheme()->getName() ?></b></span></h3>
    <div class="buttons"><button form="ThemeSettings" type="submit" class="btn btn-primary">Sauvegarder</button></div>
</div>
<!-- THEME CONFIG FILE -->

<form id="ThemeSettings" action="" method="post" enctype="multipart/form-data">
    <?php (new SecurityManager())->insertHiddenToken() ?>
    <div class="row">
        <div class="card">
            <div class="card-body">
                <?php ThemeController::getCurrentThemeConfigFile(); ?>
            </div>
        </div>
    </div>
</form>