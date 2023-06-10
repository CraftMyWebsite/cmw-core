<?php

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\UpdatesController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Updater\UpdatesManager;

/* @var UpdatesController[] $latestVersion */
/* @var UpdatesController[] $previousVersions */
/* @var UpdatesController[] $latestVersionChangelogGroup */
/* @var UpdatesController[] $previousVersionChangelogGroup */

$title = LangManager::translate("core.updates.title");
$description = LangManager::translate("core.updates.description"); ?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fas fa-arrows-rotate"></i> <span class="m-lg-auto"><?= LangManager::translate("core.updates.pageTitle") ?></span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-6">
        <div class="card">
            <?php if (UpdatesManager::checkNewUpdateAvailable()): ?>
                <div class="position-absolute end-0">
                    <a href="cms/update" type="button" class="text-bg-primary rounded-2 py-1 px-2"><?= LangManager::translate("core.updates.updateButton") ?></a>
                </div>
            <?php endif; ?>
            <div class="card-body">
                <?php if (UpdatesManager::getVersion() !== UpdatesManager::getCmwLatest()->value): ?>
                    <h5><span class="text-danger"><?= UpdatesManager::getVersion() ?></span>
                        <i
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="<?= LangManager::translate("core.updates.warningUpdate") ?>"
                            class="text-danger fa-solid fa-heart-crack fa-beat-fade"></i>
                    </h5>
                    <p><?= LangManager::translate("core.updates.updateTo") ?> <b
                            class="text-success"><?= $latestVersion["value"] ?></b> !</p>
                <?php else: ?>
                    <h5><span class="text-success"><?= UpdatesManager::getVersion() ?></span>
                        <i data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="<?= LangManager::translate("core.updates.isUp") ?>"
                           class="text-success fa-solid fa-heart-pulse fa-beat-fade"></i>
                    </h5>
                <?php endif; ?>
                <p><?= LangManager::translate("core.updates.availableFrom") ?> <?= CoreController::formatDate($latestVersion["date_upload"]) ?></p>
                <h6><?= LangManager::translate("core.updates.lastNote") ?></h6>
                <div class="card">
                    <?php foreach ($latestVersionChangelogGroup as $groupedType) : ?>
                        <h6 class="text-center p-1 rounded bg-secondary"><?= $groupedType[0]['type'] ?></h6>
                        <ul>
                            <?php foreach ($groupedType as $changelogInfos) : ?>
                                <li><?= $changelogInfos['content'] ?>
                                    <?php if ($changelogInfos['code_link']): ?>
                                    <small><a class="text-bg-primary px-1 rounded-2" href="<?= $changelogInfos['code_link'] ?>" target="_blank"><i class="fa-solid fa-link fa-xs"></i> d534333</a></small>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card">
            <div class="card-body">
                <h5><?= LangManager::translate("core.updates.previousVersion") ?></h5>
                <?php foreach ($previousVersions as $previousVersion) : ?>
                <div class="card-in-card pt-2 px-2 mb-3">
                    <div id="heading<?= $previousVersion['id'] ?>" data-bs-toggle="collapse" data-bs-target="#collapse<?= $previousVersion['id'] ?>" aria-expanded="false"
                         aria-controls="collapse<?= $previousVersion['id'] ?>" role="button">
                        <h6><i class="text-sm fa-solid fa-chevron-down"></i> <?= $previousVersion['value'] ?></h6>
                    </div>
                    <div id="collapse<?= $previousVersion['id'] ?>" class="collapse pt-1" aria-labelledby="heading<?= $previousVersion['id'] ?>" data-parent="#cardAccordion">
                        <p><?= LangManager::translate("core.updates.publishAt") ?> <?= CoreController::formatDate($previousVersion['date_upload']) ?></p>
                        <div class="card">
                            <?php foreach ($previousVersionChangelogGroup = UpdatesController::groupBy("type", $previousVersion['changelog']) as $previousGroupedType) : ?>
                            <span class="badge bg-secondary"><?= $previousGroupedType[0]['type'] ?></span>
                            <ul>
                                <?php foreach ($previousGroupedType as $previousChangelogInfos) : ?>
                                    <li><?= $previousChangelogInfos['content'] ?>
                                        <?php if ($previousChangelogInfos['code_link']): ?>
                                        <small><a class="text-bg-primary px-1 rounded-2" href="<?= $previousChangelogInfos['code_link'] ?>" target="_blank"><i class="fa-solid fa-link fa-xs"></i> d534333</a></small>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
