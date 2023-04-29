<?php

use CMW\Controller\Installer\InstallerController;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;

?>
<div class="card-body">
    <div class="grid grid-cols-2">
            <div>
                <img class="w-3/5 mx-auto" src="Installation/Views/Assets/Img/mascottefestive.png" >
            </div>
            <div>
                <p class="text-center text-4xl lg:text-7xl"><?= LangManager::translate("Installation.finish.title") ?></p>
                <p class="text-center "><?= LangManager::translate("Installation.finish.desc") ?><br></p>
                <p class="mt-4"><?= LangManager::translate("Installation.finish.review") ?></p>
                <ul style="list-style: inside;">
                    <li><?= LangManager::translate("Installation.finish.version") ?></li>
                    <li><?= LangManager::translate("Installation.finish.Theme") ?></li>
                    <li><?= LangManager::translate("Installation.finish.bundle") ?></li>
                    <li><?= LangManager::translate("Installation.finish.package") ?></li>
                </ul>
            </div>
        </div>

        <div class="card-actions justify-end">
        <a href="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>installer/finish" class="btn btn-primary"><?= LangManager::translate("Installation.finish.goToMySite") ?></a>
    </div>
</div>