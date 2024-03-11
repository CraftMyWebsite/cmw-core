<?php


use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\PackageController;
use CMW\Manager\Lang\LangManager;

/* @var PackageController[] $packagesList */

$title = LangManager::translate("core.Package.title");
$description = LangManager::translate("core.Package.desc"); ?>

<div class="d-flex flex-wrap justify-content-between">
    <h3>
        <i class="fa-solid fa-puzzle-piece"></i>
        <?= LangManager::translate("core.Theme.market") ?>
    </h3>
</div>

<div class="row">

    <!----------------------------------------
    -----Listage des packages API non nstallé---
    ------------------------------------------>
    <?php foreach ($packagesList as $apiPackages): ?>
        <?php if (!PackageController::isInstalled($apiPackages['name'])): ?>
            <div class="col-12 col-lg-6 mb-4">
                <div class="card-in-card">
                    <div class="d-flex card-body">
                        <img class="rounded-3" style="height: 180px; width: 180px;"
                             src="<?= $apiPackages["icon"] ?>" alt="img">
                        <div class="d-flex justify-content-between px-2 py-2 w-100">
                            <div>
                                <h5><b><?= $apiPackages['name'] ?></b></h5>
                                <p><?= $apiPackages['description'] ?></p>
                                <small
                                    class="align-items-end"><?= LangManager::translate("core.Package.author") ?> <?= $apiPackages['author_pseudo'] ?>
                                </small>
                            </div>
                            <div>
                                <button type="button" data-bs-toggle="modal"
                                        data-bs-target="#modal-<?= $apiPackages['id'] ?>"
                                        class="btn btn-sm btn-primary"><?= LangManager::translate("core.Package.details") ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div style="font-size: small">
                        <div class="d-flex justify-content-between px-2 py-1">
                            <div>
                                <!--TODO : rating sys-->
                                <p><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i
                                        class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i
                                        class="fa-regular fa-star"></i> (0)</p>
                                <p>Utilisé par <b><?= $apiPackages['downloads'] ?></b> utilisateurs</p>
                            </div>
                            <div class="text-end">
                                <p>Sortie le <?= CoreController::formatDate($apiPackages['date_release']) ?></p>
                                <p>Compatible avec <b><?= $apiPackages['version_cmw'] ?></b></p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center pb-2">
                        <button onclick="this.disabled = true; window.location = 'install/<?= $apiPackages['id'] ?>'"
                                class="btn btn-sm btn-primary">
                            <i class="fa-solid fa-download"></i> <?= LangManager::translate("core.Package.install") ?>
                        </button>
                    </div>

                </div>
            </div>
            <!--Details modal-->
            <div class="modal fade text-left w-100" id="modal-<?= $apiPackages['id'] ?>"
                 tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl"
                     role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><?= $apiPackages['name'] ?></h4>
                            <div class="d-flex justify-content-end mt-auto gap-3">
                                <button
                                    onclick="this.disabled = true; window.location = 'install/<?= $apiPackages['id'] ?>'"
                                    class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-download"></i> <?= LangManager::translate("core.Package.install") ?>
                                </button>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 col-lg-6 mb-3" style="height:20rem">
                                    <img style="height: 100%; width: 100%;"
                                         src="<?= $apiPackages["icon"] ?>" alt="img">
                                </div>
                                <div class="col-12 col-lg-6 position-relative">
                                    <p>
                                        <b><?= LangManager::translate("core.Package.description") ?></b><br><?= $apiPackages['description'] ?>
                                    </p>
                                    <hr>
                                    <p class="small">
                                        <?= LangManager::translate("core.Package.author") ?>
                                        <i>
                                            <b>
                                                <a href="#" target="_blank">
                                                    <?= $apiPackages['author_pseudo'] ?>
                                                </a>
                                            </b>
                                        </i>
                                        <br>
                                        <?= LangManager::translate("core.Package.downloads") ?>
                                        <i><b><?= $apiPackages['downloads'] ?></b></i>
                                    </p>
                                    <p class="small">
                                        <?= LangManager::translate("core.Package.version") ?>
                                        <i><b><?= $apiPackages['version_name'] ?></b></i><br>
                                        <?= LangManager::translate("core.Package.versionCMW") ?>
                                        <i><b><?= $apiPackages['version_cmw'] ?></b></i>
                                    </p>
                                    <div class="d-flex gap-3 align-items-center">
                                        <?php if ($apiPackages['demo']): ?>
                                            <a class="btn btn-sm btn-primary"
                                               href="<?= $apiPackages['demo'] ?>" target="_blank"><i
                                                    class="fa-solid fa-arrow-up-right-from-square"></i> <?= LangManager::translate("core.Package.demo") ?>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($apiPackages['code_link']): ?>
                                            <a class="btn btn-sm btn-primary"
                                               href="<?= $apiPackages['code_link'] ?>"
                                               target="_blank"><i class="fa-brands fa-github"></i>
                                                GitHub</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"
                                    data-bs-dismiss="modal"><?= LangManager::translate("core.Package.close") ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
