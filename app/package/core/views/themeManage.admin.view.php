<?php

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Lang\LangManager;

$title = LangManager::translate("core.theme.manage.title", ["theme" => ThemeController::getCurrentTheme()->getName()]);
$description = LangManager::translate("core.theme.manage.description", lineBreak: true); ?>


<!-- THEME CONFIG FILE -->
<div class="content">
    <div class="container-fluid">
        <?php ThemeController::getCurrentThemeConfigFile(); ?>
    </div>
</div>