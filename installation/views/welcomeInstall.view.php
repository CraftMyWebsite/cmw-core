<?php /* @var $lang String */

use CMW\Controller\Installer\InstallerController;
use CMW\Manager\Lang\LangManager;
?>
<select class="absolute top-0 right-0 select select-ghost select-sm w-32" id="lang" onchange="changeLang(this.value)">
    <option <?= $lang === 'fr' ? 'selected' : '' ?> value="fr">Français</option>
    <option <?= $lang === 'en' ? 'selected' : '' ?> value="en">English</option>
</select>

<h2 class="text-2xl font-medium text-center"><?= LangManager::translate("installation.welcome.title") ?></h2>
<p class="text-center"><?= LangManager::translate("installation.welcome.subtitle") ?></p>
<p><?= LangManager::translate("installation.welcome.config.title") ?> :</p>
<div class="overflow-x-auto">
    <table class="table w-full">
        <!-- head -->
        <thead>
        <tr class="text-center">
            <th>PHP<span class="required">*</span></th>
            <th>HTTPS</th>
            <th>PDO<span class="required">*</span></th>
            <th>ZIP<span class="required">*</span></th>
            <th>CURL<span class="required">*</span></th>
        </tr>
        </thead>
        <tbody>
        <!-- row 1 -->
        <tr class="text-center">
            <td>
                <?= InstallerController::hasRequiredFormatted('php')?>
                <?= InstallerController::$minPhpVersion . " +" ?>
            </td>
            <td>
                <?= InstallerController::hasRequiredFormatted('https')?>
            </td>
            <td>
                <?= InstallerController::hasRequiredFormatted('pdo')?>
            </td>
            <td>
                <?= InstallerController::hasRequiredFormatted('zip')?>
            </td>
            <td>
                <?= InstallerController::hasRequiredFormatted('curl')?>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<?= LangManager::translate("installation.welcome.content") ?>

<div class="card-actions justify-end">
    <form action="installer/submit" method="post" id="mainForm">
        <button id="formBtn" type="submit" class="btn btn-primary" <?= InstallerController::checkAllRequired() ? '' : 'disabled' ?>>
            <?= LangManager::translate("core.btn.next") ?>
        </button>
    </form>
</div>

<script src="installation/views/assets/js/changeLang.js"></script>