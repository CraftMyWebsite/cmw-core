<?php

use CMW\Manager\Lang\LangManager;

?>
<select class="absolute top-0 right-0 select select-ghost select-sm w-32" id="lang" onchange="changeLang(this.value)">
    <option <?= $lang === 'fr' ? 'selected' : '' ?> value="fr">Français</option>
    <option <?= $lang === 'en' ? 'selected' : '' ?> value="en">English</option>
</select>
<h2 class="text-2xl font-medium text-center"><?= LangManager::translate("Installation.config.title") ?></h2>
<form action="installer/submit" method="post" id="mainForm">
    <div class="lg:grid grid-cols-2 gap-8">
        <div>
            <h2 class="text-lg font-medium text-center"><?= LangManager::translate("Installation.config.db.db") ?></h2>
            <div class="grid grid-cols-6 gap-4 mb-2">
                <div class="col-span-4">
                    <p class="font-light"><?= LangManager::translate("Installation.config.db.address") ?> :</p>
                    <label class="input-group input-group">
                        <span><i class="fa-solid fa-server"></i></span>
                        <input type="text" value="localhost" placeholder="localhost" id="bdd_address" name="bdd_address"
                               class="input input-bordered input-sm w-full" required>
                    </label>
                </div>
                <div class="col-span-2">
                    <p class="font-light"><?= LangManager::translate("Installation.config.db.port") ?> :</p>
                    <label class="input-group input-group">
                        <span><i class="fa-solid fa-server"></i></span>
                        <input type="text" value="3306" placeholder="3306" id="bdd_port" name="bdd_port"
                               class="input input-bordered input-sm w-full" required>
                    </label>
                </div>
            </div>
            <div class="mb-2">
                <p class="font-light"><?= LangManager::translate("Installation.config.db.name") ?>:</p>
                <label class="input-group input-group">
                    <span><i class="fa-solid fa-database"></i></span>
                    <input type="text" placeholder="craftmywebsite" id="bdd_name" name="bdd_name"
                           class="input input-bordered input-sm w-full" required>
                </label>
            </div>
            <div class="mb-2">
                <p class="font-light"><?= LangManager::translate("Installation.config.db.login") ?> :</p>
                <label class="input-group input-group">
                    <span><i class="fa-solid fa-user"></i></span>
                    <input type="text" placeholder="webmaster" id="bdd_login" name="bdd_login"
                           class="input input-bordered input-sm w-full" required>
                </label>
            </div>
            <div class="mb-2">
                <p class="font-light"><?= LangManager::translate("Installation.config.db.pass") ?> :</p>
                <label class="input-group input-group">
                    <span><i class="fa-solid fa-unlock"></i></span>
                    <input type="password" placeholder="••••" id="bdd_pass" name="bdd_pass"
                           class="input input-bordered input-sm w-full">
                </label>
            </div>
            <div class="text-center">
                <button type="button" onclick="testDb()"
                        class="btn btn-primary"><?= LangManager::translate("core.btn.try") ?></button>
            </div>
        </div>
        <div>
            <h2 class="text-lg font-medium text-center"><?= LangManager::translate("Installation.config.settings.settings") ?></h2>
            <div class="mb-2">
                <p class="font-light"><?= LangManager::translate("Installation.config.settings.site_folder") ?>:</p>
                <label class="input-group input-group">
                    <span><i class="fa-regular fa-folder-open"></i></span>
                    <input type="text" placeholder="/" value="/" name="install_folder"
                           class="input input-bordered input-sm w-full" required>
                </label>
                <small><?= LangManager::translate("Installation.config.settings.site_folder_about") ?></small>
            </div>
            <div class="mt-4">
                <p class="font-light"><?= LangManager::translate("Installation.config.settings.devmode") ?> :</p>
                <input id="devmode" type="checkbox" name="dev_mode" class="checkbox checkbox-info checkbox-sm"/>
                <label for="devmode"><?= LangManager::translate("Installation.config.settings.devmode_about") ?></label>
            </div>
        </div>
    </div>
    <div class="card-actions justify-end">
        <button id="formBtn" type="submit" class="btn btn-primary">
            <?= LangManager::translate("core.btn.next") ?>
        </button>
    </div>
</form>
<script src="Installation/Views/Assets/Js/changeLang.js"></script>
<script src="Installation/Views/Assets/Js/testDb.js"></script>