<?php

use CMW\Utils\Date;
use CMW\Controller\Core\UpdatesController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Updater\UpdatesManager;

/* @var UpdatesController[] $latestVersion */
/* @var UpdatesController[] $previousVersions */
/* @var UpdatesController[] $latestVersionChangelogGroup */
/* @var UpdatesController[] $previousVersionChangelogGroup */
/* @var string $currentVersion */

$title = LangManager::translate('core.updates.title');
$description = LangManager::translate('core.updates.description');
?>

<h3><i class="fas fa-arrows-rotate"></i> <?= LangManager::translate('core.updates.pageTitle') ?></h3>

<div class="grid-2">
    <div class="card">
        <div class="flex justify-between">
            <?php if ($currentVersion !== $latestVersion['value']): ?>
            <div>
                <h3><span class="text-danger"><?= $currentVersion ?></span>
                    <i
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="<?= LangManager::translate('core.updates.warningUpdate') ?>"
                        class="text-danger fa-solid fa-heart-crack fa-beat-fade"></i>
                </h3>
                <p><?= LangManager::translate('core.updates.updateTo') ?> <b
                        class="text-success"><?= $latestVersion['value'] ?></b> !</p>
            </div>
            <?php else: ?>
            <div>
                <h3><span class="text-success"><?= $currentVersion ?></span>
                    <i data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       title="<?= LangManager::translate('core.updates.isUp') ?>"
                       class="text-success fa-solid fa-heart-pulse fa-beat-fade"></i>
                </h3>
            </div>
            <?php endif; ?>
            <?php if (UpdatesManager::checkNewUpdateAvailable()): ?>
                <a href="cms/update" class="btn-success h-fit"><?= LangManager::translate('core.updates.updateButton') ?></a>
            <?php endif; ?>
        </div>
        <p>
            <?= LangManager::translate('core.updates.availableFrom') ?>
            <?= Date::formatDate($latestVersion['date_upload']) ?>
        </p>
        <?php if ($latestVersion['notes']): ?>
            <hr>
            <h6>À noter sur cette mise à jour :</h6>
            <div class="alert-warning">
                <?= html_entity_decode($latestVersion['notes']) ?>
            </div>
        <?php endif; ?>
        <hr>
        <h6><?= LangManager::translate('core.updates.lastNote') ?></h6>
        <?php foreach ($latestVersionChangelogGroup as $groupedType): ?>
            <?php if ($groupedType[0]['content']): ?>
            <div class="border rounded-lg p-2 dark:border-gray-700">
                <p class="font-bold"><?= $groupedType[0]['type'] ?> :</p>
                <ul style="list-style: disc" class="pl-5">
                    <?php foreach ($groupedType as $changelogInfos): ?>
                        <li><?= $changelogInfos['content'] ?>
                            <?php if ($changelogInfos['code_link']): ?>
                                <small><a style="background: #4b79bf; color: white; font-size: .7rem" class="px-2 rounded-lg"
                                          href="<?= $changelogInfos['code_link'] ?>" target="_blank"><i
                                            class="fa-solid fa-link fa-xs"></i> <?= (parse_url($changelogInfos['code_link'], PHP_URL_HOST)) ?></a></small>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <div class="card">
        <h6><?= LangManager::translate('core.updates.previousVersion') ?></h6>
        <?php foreach ($previousVersions as $previousVersion): ?>
            <div class="accordion">
                <button class="accordion-btn font-bold"><?= $previousVersion['value'] ?></button>
                <div class="accordion-content">
                    <p><?= LangManager::translate('core.updates.publishAt') ?> <?= Date::formatDate($previousVersion['date_upload']) ?></p>
                    <?php foreach ($previousVersionChangelogGroup = UpdatesController::groupBy('type', $previousVersion['changelog']) as $previousGroupedType): ?>
                        <?php if ($previousGroupedType[0]['content']): ?>
                        <div class="border rounded-lg p-2 dark:border-gray-700 mb-1">
                            <p class="font-bold"><?= $previousGroupedType[0]['type'] ?> :</p>
                                <ul style="list-style: disc" class="pl-5">
                                    <?php foreach ($previousGroupedType as $previousChangelogInfos): ?>
                                        <li><?= $previousChangelogInfos['content'] ?>
                                            <?php if ($previousChangelogInfos['code_link']): ?>
                                                <small><a style="background: #4b79bf; color: white; font-size: .7rem" class="px-2 rounded-lg"
                                                          href="<?= $previousChangelogInfos['code_link'] ?>" target="_blank"><i
                                                            class="fa-solid fa-link fa-xs"></i> <?= (parse_url($previousChangelogInfos['code_link'], PHP_URL_HOST)) ?></a></small>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
