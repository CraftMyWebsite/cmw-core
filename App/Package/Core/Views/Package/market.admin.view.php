<?php

use CMW\Utils\Date;
use CMW\Controller\Core\PackageController;
use CMW\Manager\Lang\LangManager;

/* @var PackageController[] $packagesList */

$title = LangManager::translate('core.Package.title');
$description = LangManager::translate('core.Package.desc');
?>

<h3><i class="fa-solid fa-puzzle-piece"></i> <?= LangManager::translate('core.Package.market') ?></h3>

<div class="grid-2">
    <?php foreach ($packagesList as $apiPackages): ?>
        <?php if (!PackageController::isInstalled($apiPackages['name'])): ?>
            <div class="card relative h-full" style="overflow: hidden;">
                <div class="flex justify-between">
                    <img class="rounded-lg" style="height: 140px; width: 140px;"
                         src="<?= $apiPackages['icon'] ?>"
                         alt="img">
                    <div class="pl-4 w-full">
                        <div class="flex justify-between">
                            <h6><?= $apiPackages['name'] ?></h6>
                            <div>
                                <button data-modal-toggle="modal-<?= $apiPackages['id'] ?>" class="btn-primary-sm"
                                        type="button"><?= LangManager::translate('core.Package.details') ?></button>
                                <button
                                    onclick="this.disabled = true; window.location = 'install/<?= $apiPackages['id'] ?>'"
                                    class="btn-success-sm">
                                    <i class="fa-solid fa-download"></i> <?= LangManager::translate('core.Package.install') ?>
                                </button>
                            </div>
                        </div>
                        <div>
                            <p>
                                <?= mb_strimwidth($apiPackages['description_short'], 0, 280, '...') ?>
                            </p>
                            <p>
                                <?= LangManager::translate('core.Package.author') ?>
                                <a
                                    href="https://craftmywebsite.fr/market/user/<?= $apiPackages['author_pseudo'] ?>"
                                    target="_blank" class="link"><?= $apiPackages['author_pseudo'] ?>
                                </a>
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
                    <p><?= Date::formatDate($apiPackages['date_release']) ?></p>
                </div>
                <div class="flex justify-between">
                    <p>Téléchargé <b><?= $apiPackages['downloads'] ?></b> fois</p>
                    <p>Compatible avec <b><?= $apiPackages['version_cmw'] ?></b></p>
                </div>
            </div>
            <!--Details modal -->
            <div id="modal-<?= $apiPackages['id'] ?>" class="modal-container">
                <div class="modal-xl">
                    <div class="modal-header">
                        <h6><?= $apiPackages['name'] ?></h6>
                        <button
                            onclick="this.disabled = true; window.location = 'install/<?= $apiPackages['id'] ?>'"
                            class="btn-success-sm">
                            <i class="fa-solid fa-download"></i> <?= LangManager::translate('core.Package.install') ?>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="flex justify-between">
                            <img class="rounded-lg bg-contain" style="height: 200px; width: 200px;"
                                 src="<?= $apiPackages['icon'] ?>"
                                 alt="img">
                            <div class="px-4 w-full">
                                <p><b><?= LangManager::translate('core.Package.description') ?></b></p>
                                <?= htmlspecialchars_decode($apiPackages['description']) ?>
                                <p class="small">
                                    <?= LangManager::translate('core.Package.author') ?>
                                    <a
                                        href="https://craftmywebsite.fr/market/user/<?= $apiPackages['author_pseudo'] ?>"
                                        target="_blank" class="link"><?= $apiPackages['author_pseudo'] ?>
                                    </a>
                                </p>
                                <p class="small">
                                    <?= LangManager::translate('core.Package.version') ?>
                                    <i><b><?= $apiPackages['version_name'] ?></b></i><br>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-modal-hide="modal-<?= $apiPackages['id'] ?>" type="button"
                                class="btn-primary"><?= LangManager::translate('core.Package.close') ?></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>