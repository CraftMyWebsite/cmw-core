<?php

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;

$title = LangManager::translate("core.updates.title");
$description = LangManager::translate("core.updates.description"); ?>


<div class="content">
    <div class="container-fluid">

        <p>Bonjour,<br>
            Version actuelle: <b><?= Utils::getVersion() ?></b></p>
        <p>Version distante: <b><?= Utils::getLatestVersion() ?></b></p>

        <div>
            <h3>Changelog</h3>
            <p>blabla
            </p>
        </div>

        <?php if (Utils::isNewUpdateAvailable()): ?>
            <a href="cms/install" class="btn btn-success">
                Mettez à jour votre cms
            </a>
        <?php endif; ?>
    </div>
</div>