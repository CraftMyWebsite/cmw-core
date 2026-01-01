<?php

use CMW\Controller\Core\PackageController;
use CMW\Controller\Core\ThemeController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Theme\Loader\ThemeLoader;
use CMW\Manager\Theme\ThemeManager;
use CMW\Manager\Updater\UpdatesManager;

?>
<div class="card-body">
    <div class="grid grid-cols-2">
        <div>
            <img class="w-3/5 mx-auto" src="Installation/Views/Assets/Img/mascottefestive.png">
        </div>
        <div>
            <p class="text-center text-4xl lg:text-7xl"><?= LangManager::translate('Installation.finish.title') ?></p>
            <p class="text-center "><?= LangManager::translate('Installation.finish.desc') ?><br></p>
            <p class="mt-4"><?= LangManager::translate('Installation.finish.review') ?></p>
            <ul style="list-style: outside;">
                <li><?= LangManager::translate('Installation.finish.version') ?>
                    <b><?= UpdatesManager::getVersion() ?></b>
                </li>
                <li><?= LangManager::translate('Installation.finish.Theme') ?>
                    <b><?= ThemeLoader::getInstance()->getCurrentTheme()->name() . ' - v' . ThemeLoader::getInstance()->getCurrentTheme()->version() ?></b>
                </li>
                <li><?= LangManager::translate('Installation.finish.package') ?>
                    <ul style="list-style: inside;">
                        <?php foreach (PackageController::getInstalledPackages() as $installedPackage): ?>
                            <li><b><?= $installedPackage->name() . ' - v' . $installedPackage->version() ?></b>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

    <div class="card-actions justify-end">
        <a href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>installer/finish" class="btn btn-primary">
            <?= LangManager::translate('Installation.finish.goToMySite') ?>
        </a>
    </div>
</div>