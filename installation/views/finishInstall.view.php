<?php

use CMW\Controller\Installer\InstallerController;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;

?>
<select class="absolute top-0 right-0 select select-ghost select-sm w-32" id="lang" onchange="changeLang(this.value)">
    <option <?= $lang === 'fr' ? 'selected' : '' ?> value="fr">Français</option>
    <option <?= $lang === 'en' ? 'selected' : '' ?> value="en">English</option>
</select>
<div class="card-body">
    <div class="grid grid-cols-2">
            <div>
                <img class="w-3/5 mx-auto" src="installation/views/assets/img/mascottefestive.png" >
            </div>
            <div>
                <p class="text-center text-4xl lg:text-7xl"><?= LangManager::translate("installation.finish.title") ?></p>
                <p class="text-center "><?= LangManager::translate("installation.finish.desc") ?><br></p>
                <p class="mt-4"><?= LangManager::translate("installation.finish.review") ?></p>
                <ul style="list-style: inside;">
                    <li><?= LangManager::translate("installation.finish.version") ?></li>
                    <li><?= LangManager::translate("installation.finish.theme") ?></li>
                    <li><?= LangManager::translate("installation.finish.bundle") ?></li>
                    <li><?= LangManager::translate("installation.finish.package") ?></li>
                </ul>
            </div>
        </div>

        <div class="card-actions justify-end">
        <a href="<?= Utils::getEnv()->getValue('PATH_SUBFOLDER') ?>installer/finish" class="btn btn-primary"><?= LangManager::translate("installation.finish.goToMySite") ?></a>
    </div>
</div>
<script src="installation/views/assets/js/changeLang.js"></script>