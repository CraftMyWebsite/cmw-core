<?php

use CMW\Utils\Date;
use CMW\Controller\Core\PackageController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;

/* @var PackageController[] $packagesList */

$title = LangManager::translate('core.Package.title');
$description = LangManager::translate('core.Package.desc');
?>

<h3><i class="fa-solid fa-puzzle-piece"></i> <?= LangManager::translate('core.Package.my_packages') ?></h3>

<div class="grid-2">
    <!------------------------------------
        -----Listage des packages local installé---
        -------------------------------------->
    <?php foreach (PackageController::getLocalPackages() as $packages): ?>
    <?php if ($packages->name() !== 'Pages'): ?>
        <div class="card relative h-full" style="overflow: hidden;">
            <div class="flex justify-between">
                <img class="rounded-lg" style="height: 140px; width: 140px;"
                     src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Img/local-theme.jpg"
                     alt="img">
                <div class="pl-4">
                    <div class="flex justify-between">
                        <h6><?= $packages->name() ?></h6>
                        <div>
                            <button data-modal-toggle="delete-<?= $packages->name() ?>" class="btn-danger-sm"
                                    type="button"><?= LangManager::translate('core.Package.delete') ?></button>
                            <button data-modal-toggle="modal-<?= $packages->name() ?>" class="btn-primary-sm"
                                    type="button"><?= LangManager::translate('core.Package.details') ?></button>
                        </div>
                    </div>
                    <div>
                        <p>
                            <?= LangManager::translate('core.Package.descriptionNotAvailable') ?>
                        </p>
                        <small class="align-items-end">
                            <?= LangManager::translate('core.Package.author') ?>
                            <?= 'TODO' ?>
                        </small>
                    </div>

                </div>
            </div>
            <div class="absolute"
                 style="transform: rotate(-45deg); left: -4.3em; top: 3.3em; margin: 0; z-index: 10">
                <div class="bg-warning text-center px-16" style="opacity: .85">
                    <?= LangManager::translate('core.Package.notVerified') ?>
                </div>
            </div>
        </div>
        <!--Details modal -->
        <div id="modal-<?= $packages->name() ?>" class="modal-container">
            <div class="modal-lg">
                <div class="modal-header">
                    <h6><?= $packages->name() ?></h6>
                    <button type="button" data-modal-hide="modal-<?= $packages->name() ?>"><i
                            class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <div class="flex justify-between">
                        <img class="rounded-lg bg-contain" style="height: 200px; width: 200px;"
                             src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Assets/Img/local-theme.jpg"
                             alt="img">
                        <div class="px-4">
                            <p><b><?= LangManager::translate('core.Package.description') ?></b></p>
                            <p><?= LangManager::translate('core.Package.descriptionNotAvailable') ?></p>
                            <hr>
                            <p class="small">
                                <?= LangManager::translate('core.Package.author') ?>
                                <a href="" target="_blank">
                                    <?= 'TODO' ?>
                                </a>
                            </p>
                            <p class="small">
                                <?= LangManager::translate('core.Package.version') ?>
                                <i><b><?= $packages->version() ?></b></i><br>

                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-modal-hide="modal-<?= $packages->name() ?>" type="button"
                            class="btn-primary"><?= LangManager::translate('core.Package.close') ?></button>
                </div>
            </div>
        </div>
        <!--Delete modal -->
        <div id="delete-<?= $packages->name() ?>" class="modal-container">
            <div class="modal">
                <div class="modal-header-danger">
                    <h6><?= LangManager::translate('core.Package.removeTitle',
            ['package' => $packages->name()]) ?></h6>
                    <button type="button" data-modal-hide="delete-<?= $packages->name() ?>"><i
                            class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <?= LangManager::translate('core.Package.removeText') ?>
                </div>
                <div class="modal-footer">
                    <a href="delete/<?= $packages->name() ?>" type="button"
                       class="btn-danger"><?= LangManager::translate('core.Package.delete') ?></a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php endforeach; ?>
    <!------------------------------------
        -----Listage des packages API installé---
        -------------------------------------->
    <?php foreach ($packagesList as $packages): ?>
        <?php if (PackageController::isInstalled($packages['name'])): ?>
            <?php $localPackage = PackageController::getPackage($packages['name']); ?>
            <div class="card relative h-full" style="overflow: hidden;">
                <div class="flex justify-between">
                    <img class="rounded-lg" style="height: 140px; width: 140px;"
                         src="<?= $packages['icon'] ?>"
                         alt="img">
                    <div class="pl-4 w-full">
                        <div class="flex justify-between">
                            <h6><?= $packages['name'] ?></h6>
                            <div>
                                <button data-modal-toggle="delete-<?= $packages['id'] ?>" class="btn-danger-sm"
                                        type="button"><?= LangManager::translate('core.Package.delete') ?></button>
                                <?php if ($localPackage->version() !== $packages['version_title']): ?>
                                    <a class="btn-warning-sm" type="button"
                                       href="update/<?= $packages['id'] ?>/<?= $localPackage->version() ?>/<?= $localPackage->name() ?>">
                                        <?= LangManager::translate('core.Package.update') ?>
                                    </a>
                                <?php else: ?>
                                    <button data-modal-toggle="modal-<?= $packages['id'] ?>" class="btn-primary-sm"
                                            type="button"><?= LangManager::translate('core.Package.details') ?></button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <p>
                                <?= mb_strimwidth($packages['description_short'], 0, 280, '...') ?>
                            </p>
                            <p>
                                <?= LangManager::translate('core.Package.author') ?>
                                <?= $packages['author_pseudo'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="flex justify-between">
                    <p><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i
                            class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i
                            class="fa-regular fa-star"></i> (0)
                    </p>
                    <p><?= Date::formatDate($packages['date_release']) ?></p>
                </div>
                <div class="flex justify-between">
                    <p>Téléchargé <b><?= $packages['downloads'] ?></b> fois</p>
                    <p>Compatible avec <b><?= $packages['version_cmw'] ?></b></p>
                </div>
                <?php if ($localPackage->version() !== $packages['version_title']): ?>
                    <div class="absolute"
                         style="transform: rotate(-45deg); left: -4.3em; top: 3.3em; margin: 0; z-index: 10">
                        <div class="bg-warning text-center px-16" style="opacity: .85">
                            <?= LangManager::translate('core.Theme.update') ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <!--Details modal -->
            <div id="modal-<?= $packages['id'] ?>" class="modal-container">
                <div class="modal-lg">
                    <div class="modal-header">
                        <h6><?= $packages['name'] ?></h6>
                        <button type="button" data-modal-hide="modal-<?= $packages['id'] ?>"><i
                                class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="flex justify-between">
                            <img class="rounded-lg bg-contain" style="height: 200px; width: 200px;"
                                 src="<?= $packages['icon'] ?>"
                                 alt="img">
                            <div class="px-4 w-full">
                                <p><b><?= LangManager::translate('core.Package.description') ?></b></p>
                                <?= html_entity_decode($packages['description']) ?>
                                <p class="small">
                                    <?= LangManager::translate('core.Package.author') ?>
                                    <?= $packages['author_pseudo'] ?>
                                </p>
                                <p class="small">
                                    <?= LangManager::translate('core.Package.version') ?>
                                    <i><b><?= $packages['version_name'] ?></b></i><br>
                                </p>
                                <?php if ($localPackage->version() !== $packages['version_title']): ?>
                                    <div class="position-absolute"
                                         style="transform: rotate(-45deg); left: -4em; top: 3.9em; margin: 0; z-index: 50">
                                        <div class="bg-warning text-center px-5"
                                             style="opacity: .85;">
                                            <?= LangManager::translate('core.Theme.update') ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-modal-hide="modal-<?= $packages['id'] ?>" type="button"
                                class="btn-primary"><?= LangManager::translate('core.Package.close') ?></button>
                    </div>
                </div>
            </div>
            <!--Delete modal -->
            <div id="delete-<?= $packages['id'] ?>" class="modal-container">
                <div class="modal">
                    <div class="modal-header-danger">
                        <h6><?= LangManager::translate('core.Package.removeTitle',
            ['package' => $packages['name']]) ?></h6>
                        <button type="button" data-modal-hide="delete-<?= $packages['id'] ?>"><i
                                class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <?= LangManager::translate('core.Package.removeText') ?>
                    </div>
                    <div class="modal-footer">
                        <a href="delete/<?= $packages['name'] ?>" type="button"
                           class="btn-danger"><?= LangManager::translate('core.Package.delete') ?></a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>