<?php
/* @var \CMW\Controller\Installer\InstallerController $install */

use CMW\Manager\Lang\LangManager;

?>
<select class="absolute top-0 right-0 select select-ghost select-sm w-32" id="lang" onchange="changeLang(this.value)">
    <option <?= $lang === 'fr' ? 'selected' : '' ?> value="fr">Français</option>
    <option <?= $lang === 'en' ? 'selected' : '' ?> value="en">English</option>
</select>

<h2 class="text-2xl font-medium text-center"><?= LangManager::translate("Installation.details.title") ?></h2>
<form action="installer/submit" method="post" id="mainForm">
    <div class="lg:grid grid-cols-2 gap-8">
        <div class="form-control">
            <p class="font-light"><?= LangManager::translate("Installation.details.website.name") ?> :</p>
            <label class="input-group input-group">
                <span><i class="fa-solid fa-heading"></i></span>
                <input type="text" placeholder="CraftMyWebsite" maxlength="255" name="config_name"
                       class="input input-bordered input-sm w-full" required>
            </label>
        </div>
        <div class="form-control">
            <p class="font-light"><?= LangManager::translate("Installation.details.website.description") ?> :</p>
            <label class="input-group input-group">
                <span><i class="fa-solid fa-text-width"></i></i></span>
                <input type="text" maxlength="255" class="input input-bordered input-sm w-full" name="config_description"
                       placeholder="<?= LangManager::translate("Installation.details.website.description_placeholder") ?>"
                       required>
            </label>
        </div>
    </div>
    <div class="card-actions justify-end">
        <button id="formBtn" type="submit" class="btn btn-primary">
            <?= LangManager::translate("core.btn.next") ?>
        </button>
    </div>
</form>
<script src="installation/Views/Assets/Js/changeLang.js"></script>