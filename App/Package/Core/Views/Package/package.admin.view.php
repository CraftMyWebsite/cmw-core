<?php

use CMW\Utils\Date;
use CMW\Controller\Core\PackageController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;

/* @var PackageController[] $packagesList */

$title = LangManager::translate('core.Package.title');
$description = LangManager::translate('core.Package.desc');


$packagesToUpdate = [];
$packagesUpToDate = [];

foreach ($packagesList as $pkg) {
    if (!PackageController::isInstalled($pkg['name'])) {
        continue;
    }
    $local = PackageController::getPackage($pkg['name']);
    if ($pkg['version_status'] === 0 && $local->version() !== $pkg['version_name']) {
        $packagesToUpdate[] = $pkg;
    } else {
        $packagesUpToDate[] = $pkg;
    }
}


function renderCard($name, $image, $description, $author = null, $version = null, $id = null, $notVerified = false, $updateBadge = false, $downloads = null, $versionCMW = null, $releaseDate = null) {
    $uniqueId = $id ?? $name;
    ?>
    <div class="card relative h-full" style="overflow: hidden;">
        <div class="flex justify-between">
            <img class="rounded-lg" style="height: 140px; width: 140px;" src="<?= $image ?>" alt="img">
            <div class="pl-4 w-full">
                <div class="flex justify-between">
                    <h6><?= $name ?></h6>
                    <div>
                        <button data-modal-toggle="delete-<?= $uniqueId ?>" class="btn-danger-sm" type="button">
                            <?= LangManager::translate('core.Package.delete') ?>
                        </button>
                        <button data-modal-toggle="modal-<?= $uniqueId ?>" class="btn-primary-sm" type="button">
                            <?= LangManager::translate('core.Package.details') ?>
                        </button>
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
                    <?= LangManager::translate('core.theme.update') ?>
                </div>
            </div>
        <?php elseif ($notVerified): ?>
            <div class="absolute" style="transform: rotate(-45deg); left: -4.3em; top: 3.3em; z-index: 10">
                <div class="bg-warning text-center px-16" style="opacity: .85">
                    <?= LangManager::translate('core.Package.notVerified') ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($downloads !== null && $versionCMW): ?>
            <hr>
            <div class="flex justify-between">
                <p>Téléchargé <b><?= $downloads ?></b> fois</p>
                <p>Compatible avec <b><?= $versionCMW ?></b></p>
            </div>
            <div class="flex justify-between">
                <p>
                    <i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i>
                    <i class="fa-regular fa-star"></i> (0)
                </p>
                <p><?= Date::formatDate($releaseDate) ?></p>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function renderModalDetails($id, $name, $image, $description, $author = null, $localVersion = null, $onlineVersion = null, $versionStatus = null) {
    ?>
    <div id="modal-<?= $id ?>" class="modal-container">
        <div class="modal-lg">
            <div class="modal-header">
                <h6><?= $name ?></h6>
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

<div class="grid-2">
    <!-- Packages API - à mettre à jour -->
    <?php foreach ($packagesToUpdate as $pkg): ?>
        <?php
        $local = PackageController::getPackage($pkg['name']);
        renderCard(
            $pkg['name'],
            $pkg['icon'],
            mb_strimwidth($pkg['description_short'], 0, 280, '...'),
            $pkg['author_pseudo'],
            $local->version(),
            $pkg['id'],
            false,
            true,
            $pkg['downloads'],
            $pkg['version_cmw'],
            $pkg['date_release']
        );
        renderModalDetails(
            $pkg['id'],
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
            $pkg['name'],
            $pkg['icon'],
            mb_strimwidth($pkg['description_short'], 0, 280, '...'),
            $pkg['author_pseudo'],
            $local->version(),
            $pkg['id'],
            false,
            false,
            $pkg['downloads'],
            $pkg['version_cmw'],
            $pkg['date_release']
        );
        renderModalDetails(
            $pkg['id'],
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
            renderCard($pkg->name(), $img, LangManager::translate('core.Package.descriptionNotAvailable'), null, $pkg->version(), null, true);
            renderModalDetails($pkg->name(), $pkg->name(), $img, LangManager::translate('core.Package.descriptionNotAvailable'));
            renderModalDelete($pkg->name(), $pkg->name());
            ?>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
