<?php /* @var $lang String */

use CMW\Controller\Installer\InstallerController;
use CMW\Manager\Lang\LangManager;

?>
<h2 class="text-2xl font-medium text-center"><?= LangManager::translate("Installation.welcome.title") ?></h2>
<p class="text-center"><?= LangManager::translate("Installation.welcome.subtitle") ?></p>
<p><?= LangManager::translate("Installation.welcome.config.title") ?> :</p>
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
                <?= InstallerController::hasRequiredFormatted('php') ?>
                <?= InstallerController::$minPhpVersion . " +" ?>
            </td>
            <td>
                <?= InstallerController::hasRequiredFormatted('https') ?>
            </td>
            <td>
                <?= InstallerController::hasRequiredFormatted('pdo') ?>
            </td>
            <td>
                <?= InstallerController::hasRequiredFormatted('zip') ?>
            </td>
            <td>
                <?= InstallerController::hasRequiredFormatted('curl') ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<?= LangManager::translate("Installation.welcome.content") ?>
<form action="installer/submit" method="post" id="mainForm">
    <div class="form-control">
        <label class="label cursor-pointer">
            <input id="cgu" name="cgu" type="checkbox" class="checkbox checkbox-primary checkbox-xs"/>
            <span class=""><?= LangManager::translate("Installation.welcome.readaccept") ?> <i><a
                        class="text-gray-400 hover:text-primary" target="_blank"
                        href="https://craftmywebsite.fr/cgu"><?= LangManager::translate("Installation.welcome.cgu") ?></a></i></span>
        </label>
    </div>
    <div class="card-actions justify-end">
        <button disabled id="formBtn" type="submit"
                class="btn btn-primary" <?= InstallerController::checkAllRequired() ? '' : 'disabled' ?>>
            <?= LangManager::translate("core.btn.next") ?>
        </button>
    </div>
</form>

<script>
    const cguCheckbox = document.getElementById('cgu');

    cguCheckbox.addEventListener('change', e => {
        if (e.target.checked === true) {
            document.getElementById("formBtn").disabled = false;
        }
        if (e.target.checked === false) {
            document.getElementById("formBtn").disabled = true;
        }
    });
</script>