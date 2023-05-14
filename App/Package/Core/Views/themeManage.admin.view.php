<?php

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

$title = LangManager::translate("core.Theme.manage.title", ["Theme" => ThemeController::getCurrentTheme()->getName()]);
$description = LangManager::translate("core.Theme.manage.description"); ?>

<div class="d-flex flex-wrap justify-content-between" style="width: 100%">
    <h3><i class="fa-solid fa-pen-nib"></i> <span class="m-lg-auto">Apparence de <b><?= ThemeController::getCurrentTheme()->getName() ?></b></span></h3>
    <div class="d-flex flex-wrap justify-content-end">
        <form class="me-4" action="market/regenerate" method="post">
            <?php (new SecurityManager())->insertHiddenToken() ?>
            <button type="submit" class="btn btn-warning ">Réinitialiser</button>
        </form>
        <div>
            <button form="ThemeSettings" type="submit" class="btn btn-primary">Sauvegarder</button>
        </div>
    </div>
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