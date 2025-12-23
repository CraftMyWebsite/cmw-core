<?php

use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Utils\Date;
use CMW\Controller\Core\PackageController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;

/* @var PackageController[] $packagesList */

$title = LangManager::translate('core.Package.title');
$description = LangManager::translate('core.Package.desc');

$packagesToUpdate = [];
$packagesUpToDate = [];

$testMode = UpdatesManager::isTestAPI();

foreach ($packagesList as $pkg) {
    if (!PackageController::isInstalled($pkg['name'])) {
        continue;
    }
    $local = PackageController::getPackage($pkg['name']);
    $needsUpdate = false;

    // Mode normal: uniquement versions stables
    if (!$testMode && $pkg['version_status'] === 0 && $local->version() !== $pkg['version_name']) {
        $needsUpdate = true;
    }

    // Mode test: on autorise aussi les versions en attente
    if ($testMode && $local->version() !== $pkg['version_name']) {
        $needsUpdate = true;
    }

    if ($needsUpdate) {
        $packagesToUpdate[] = $pkg;
    } else {
        $packagesUpToDate[] = $pkg;
    }
}

function renderCard($marketName, $name, $image, $description, $author = null, $versionStatus = null, $versionTarget = null, $version = null, $id = null, $notVerified = false, $updateBadge = false, $downloads = null, $versionCMW = null, $releaseDate = null, $rate = null, $targetStatusSlug = 'online'
) {
    $uniqueId = $id ?? $name;
    ?>
    <div class="card relative h-full" style="overflow: hidden;">
        <div class="flex justify-between">
            <img class="rounded-lg" style="height: 140px; width: 140px;" src="<?= $image ?>" alt="img">
            <div class="pl-4 w-full">
                <div class="flex justify-between">
                    <h6><?= ($versionStatus) === 1  && UpdatesManager::isTestAPI() ? '<span class="text-warning">En attente : </span>' : '' ?><?= $marketName ?></h6>
                    <div>
                        <?php if ($updateBadge): ?>
                            <form action="update" method="post" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                <input type="hidden" name="resId" value="<?= $id ?>">
                                <input type="hidden" name="localVersion" value="<?= $version ?>">
                                <input type="hidden" name="packageName" value="<?= $name ?>">
                                <input type="hidden" name="status" value="<?= $targetStatusSlug ?>">
                                <button type="submit" class="btn-warning-sm"><?= LangManager::translate('core.Package.update') ?></button>
                            </form>
                        <?php else: ?>
                            <button data-modal-toggle="delete-<?= $uniqueId ?>" class="btn-danger-sm" type="button">
                                <?= LangManager::translate('core.Package.delete') ?>
                            </button>
                            <button data-modal-toggle="modal-<?= $uniqueId ?>" class="btn-primary-sm" type="button">
                                <?= LangManager::translate('core.Package.details') ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <p><?= $description ?></p>
                    <?php if ($author): ?>
                        <p><?= LangManager::translate('core.Package.author') ?>
                            <a href="https://craftmywebsite.fr/market/user/<?= $author ?>" target="_blank" class="link">
                                <?= $author ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($updateBadge): ?>
            <div class="absolute" style="transform: rotate(-45deg); left: -4.3em; top: 3.3em; z-index: 10">
                <div class="bg-warning text-center px-16" style="opacity: .85">
                    <?= (UpdatesManager::isTestAPI() && $versionStatus === 1) ? 'MAJ de TEST dispo' : LangManager::translate('core.theme.update') ?>
                </div>
            </div>
        <?php elseif ($notVerified): ?>
            <div class="absolute" style="transform: rotate(-45deg); left: -4.3em; top: 3.3em; z-index: 10">
                <div class="bg-warning text-center px-16" style="opacity: .85">
                    <?= LangManager::translate('core.Package.notVerified') ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($updateBadge): ?>
            <div class="alert-warning text-center">
                <?= LangManager::translate('core.theme.manage.theme_need_update',
                    ['version' => $version, 'target' => $versionTarget]) ?>
            </div>
        <?php endif; ?>

        <?php if ($downloads !== null && $versionCMW): ?>
            <hr>
            <div class="flex justify-between">
                <p>Téléchargé <b><?= PackageController::getInstance()->formatDownloads((int) $downloads) ?></b> fois</p>
                <p>Compatible avec <b><?= $versionCMW ?></b></p>
            </div>
            <div class="flex justify-between">
                <p>
                    <?= PackageController::getInstance()->renderStars($rate) ?> <?= $rate ? $rate . '/5' : '' ?>
                </p>
                <p><?= Date::formatDate($releaseDate) ?></p>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function renderModalDetails($id, $marketName, $name, $image, $description, $author = null, $localVersion = null, $onlineVersion = null, $versionStatus = null) {
    ?>
    <div id="modal-<?= $id ?>" class="modal-container">
        <div class="modal-lg">
            <div class="modal-header">
                <h6><?= $marketName ?></h6>
                <button type="button" data-modal-hide="modal-<?= $id ?>"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <div class="flex justify-between">
                    <img class="rounded-lg bg-contain" style="height: 200px; width: 200px;" src="<?= $image ?>" alt="img">
                    <div class="px-4 w-full">
                        <p><b><?= LangManager::translate('core.Package.description') ?></b></p>
                        <?= $description ?>
                        <?php if ($author): ?>
                            <p class="small"><?= LangManager::translate('core.Package.author') ?>
                                <a href="https://craftmywebsite.fr/market/user/<?= $author ?>" target="_blank" class="link"><?= $author ?></a>
                            </p>
                        <?php endif; ?>
                        <?php if ($localVersion !== null && $onlineVersion !== null): ?>
                            <p class="small">
                                <?= LangManager::translate('core.Package.localPackageVersion') ?> <b><?= $localVersion ?></b><br>
                                <?= LangManager::translate('core.Package.version') ?> <b><?= $onlineVersion ?></b>
                                <?php if ($versionStatus !== 0): ?>
                                    <small class="text-warning">En cours de vérification</small>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button data-modal-hide="modal-<?= $id ?>" type="button" class="btn-primary"><?= LangManager::translate('core.Package.close') ?></button>
            </div>
        </div>
    </div>
    <?php
}

function renderModalDelete($id, $name) {
    ?>
    <div id="delete-<?= $id ?>" class="modal-container">
        <div class="modal">
            <div class="modal-header-danger">
                <h6><?= LangManager::translate('core.Package.removeTitle', ['package' => $name]) ?></h6>
                <button type="button" data-modal-hide="delete-<?= $id ?>"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="modal-body">
                <?= LangManager::translate('core.Package.removeText') ?>
            </div>
            <div class="modal-footer">
                <a href="delete/<?= $name ?>" type="button" class="btn-danger"><?= LangManager::translate('core.Package.delete') ?></a>
            </div>
        </div>
    </div>
    <?php
}
?>

<h3><i class="fa-solid fa-puzzle-piece"></i> <?= LangManager::translate('core.Package.my_packages') ?></h3>

<?php if ($testMode):?>
    <h6 class="text-warning mb-2">Votre site est en mode test API.</h6>
<?php endif; ?>

<div class="grid-2">
    <!-- Packages API - à mettre à jour -->
    <?php foreach ($packagesToUpdate as $pkg): ?>
        <?php
        $local = PackageController::getPackage($pkg['name']);
        // Si mode test et version en attente -> proposer update sur flux test
        $targetStatusSlug = ($testMode && $pkg['version_status'] === 1) ? 'test' : 'online';

        renderCard(
            $pkg['market_name'],
            $pkg['name'],
            $pkg['icon'],
            mb_strimwidth($pkg['description_short'], 0, 280, '...'),
            $pkg['author_pseudo'],
            $pkg['version_status'],
            $pkg['version_name'],
            $local->version(),
            $pkg['id'],
            false,
            true,
            $pkg['downloads'],
            $pkg['version_cmw'],
            $pkg['date_release'],
            $pkg['rate'],
            $targetStatusSlug
        );
        renderModalDetails(
            $pkg['id'],
            $pkg['market_name'],
            $pkg['name'],
            $pkg['icon'],
            html_entity_decode($pkg['description']),
            $pkg['author_pseudo'],
            $local->version(),
            $pkg['version_name'],
            $pkg['version_status']
        );
        renderModalDelete($pkg['id'], $pkg['name']);
        ?>
    <?php endforeach; ?>

    <!-- Packages API - à jour -->
    <?php foreach ($packagesUpToDate as $pkg): ?>
        <?php
        $local = PackageController::getPackage($pkg['name']);
        renderCard(
            $pkg['market_name'],
            $pkg['name'],
            $pkg['icon'],
            mb_strimwidth($pkg['description_short'], 0, 280, '...'),
            $pkg['author_pseudo'],
            $pkg['version_status'],
            $pkg['version_name'],
            $local->version(),
            $pkg['id'],
            false,
            false,
            $pkg['downloads'],
            $pkg['version_cmw'],
            $pkg['date_release'],
            $pkg['rate'],
        );
        renderModalDetails(
            $pkg['id'],
            $pkg['market_name'],
            $pkg['name'],
            $pkg['icon'],
            html_entity_decode($pkg['description']),
            $pkg['author_pseudo'],
            $local->version(),
            $pkg['version_name'],
            $pkg['version_status']
        );
        renderModalDelete($pkg['id'], $pkg['name']);
        ?>
    <?php endforeach; ?>

    <!-- Packages locaux -->
    <?php foreach (PackageController::getLocalPackages() as $pkg): ?>
        <?php if ($pkg->name() !== 'Pages'): ?>
            <?php
            $img = EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'Admin/Resources/Assets/Img/local-theme.jpg';
            renderCard($pkg->name(), $pkg->name(), $img, LangManager::translate('core.Package.descriptionNotAvailable'), null, $pkg->version(), null, true);
            renderModalDetails($pkg->name(), $pkg->name(), $pkg->name(), $img, LangManager::translate('core.Package.descriptionNotAvailable'));
            renderModalDelete($pkg->name(), $pkg->name());
            ?>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('button[type="submit"]');

        buttons.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                const form = this.closest('form');

                buttons.forEach(b => b.disabled = true);

                this.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Veuillez patienter';

                form.submit();
            });
        });
    });
</script>