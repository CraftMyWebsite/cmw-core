<?php  /* @var $lang String */

use CMW\Controller\Installer\InstallerController;
use CMW\Manager\Lang\LangManager;

$pathsToCheck = [
    'App/Storage',
    'Public/Themes',
    'Public/Uploads',
    'App/Package',
    '.env',
    'robots.txt',
    'sitemap.xml',
];

$results = [];

foreach ($pathsToCheck as $path) {
    if (is_dir($path)) {
        $results[$path] = is_writable($path) ? 1 : LangManager::translate('Installation.welcome.folder_check_no_access');
    } elseif (file_exists($path)) {
        $results[$path] = is_writable($path) ? 1 : LangManager::translate('Installation.welcome.folder_check_no_access');
    } else {
        $results[$path] = LangManager::translate('Installation.welcome.folder_check_not_found');
    }
}

$founded = false;
foreach ($results as $status) {
    if ($status !== 1) {
        $founded = true;
        break;
    }
}

?>
<h2 class="text-2xl font-medium text-center"><?= LangManager::translate('Installation.welcome.title') ?></h2>
<p class="text-center"><?= LangManager::translate('Installation.welcome.subtitle') ?></p>
<p><?= LangManager::translate('Installation.welcome.config.title') ?> :</p>

<?php if ($founded): ?>
<div style="background: #2A303C; padding: .7rem; border-radius: .5rem; margin-bottom: 1rem">
    <div style="background: #9e3640; padding: .3rem; border-radius: .3rem; margin-bottom: .6rem; text-align: center">
        <b><?= LangManager::translate('Installation.welcome.folder_check_fix_it') ?></b>
    </div>

    <?php foreach ($results as $path => $status): ?>
        <?php if ($status !== 1): ?>
            <div style="background: #c5761b; padding: .3rem; border-radius: .3rem; margin-bottom: .6rem">
                <b>*<?= $path ?>*</b> : <?= $status ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <div style="display: flex; justify-content: center">
        <button onclick="refreshPage();" style="background: #568f0b; color: #fff; border: none; padding: .3rem 1rem; border-radius: .3rem; cursor: pointer;">
            <i id="wait" style="display: none; margin-right: .3rem" class="fa-solid fa-spinner fa-spin"></i> <?= LangManager::translate('Installation.welcome.folder_check_button') ?>
        </button>
    </div>
</div>
<?php endif; ?>

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
                <?= InstallerController::$minPhpVersion . ' +' ?>
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

<?= LangManager::translate('Installation.welcome.content') ?>
<form action="installer/submit" method="post" id="mainForm">
    <div class="form-control">
        <label class="label cursor-pointer">
            <input id="cgu" name="cgu" type="checkbox" class="checkbox checkbox-primary checkbox-xs"/>
            <span class=""><a
                    class="text-gray-400 hover:text-primary" target="_blank"
                    href="https://craftmywebsite.fr/cgu"><?= LangManager::translate('Installation.welcome.readaccept') ?> <i></a></i></span>
        </label>
    </div>
    <div class="card-actions justify-end">
        <button disabled id="formBtn" type="submit"
                class="btn btn-primary" <?= InstallerController::checkAllRequired() ? '' : 'disabled' ?>>
            <?= LangManager::translate('core.btn.next') ?>
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

<script>
    function refreshPage() {
        // Afficher le loader
        document.getElementById('wait').style.display = 'inline-block';

        // Rafraîchir la page après une courte pause pour que le loader soit visible
        setTimeout(() => {
            location.reload();
        }, 500); // Optionnel : attendre 500 ms avant le rafraîchissement
    }
</script>