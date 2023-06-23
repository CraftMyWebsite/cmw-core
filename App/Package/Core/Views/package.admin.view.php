<?php


use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\PackageController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;

/* @var \CMW\Entity\Core\PackageEntity[] $installedPackages */
/* @var PackageController[] $packagesList */

$title = LangManager::translate("core.Package.title");
$description = LangManager::translate("core.Package.desc"); ?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-puzzle-piece"></i> <span
            class="m-lg-auto"><?= LangManager::translate("core.Package.title") ?></span></h3>
</div>

<div class="row">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="setting1-tab" data-bs-toggle="tab" href="#setting1" role="tab"
                       aria-selected="true"><?= LangManager::translate("core.Package.myPackages") ?></a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="setting2-tab" data-bs-toggle="tab" href="#setting2" role="tab"
                       aria-selected="false"><?= LangManager::translate("core.Package.market") ?></a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active py-2" id="setting1" role="tabpanel"
                     aria-labelledby="setting1-tab">
                    <div class="row">
                        <!------------------------------------
                        -----Listage des packages local installé---
                        -------------------------------------->
                        <?php foreach (PackageController::getLocalPackages() as $packages): ?>
                            <div class="col-12 col-lg-6 mb-4">
                                <div class="card-in-card" style="overflow: hidden;">
                                    <div class="d-flex card-body">
                                        <img class="rounded-3 " style="height: 180px; width: 180px;"
                                             src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Default/local-theme.jpg"
                                             alt="img">
                                        <div class="d-flex justify-content-between px-2 py-2 w-100">
                                            <div>
                                                <h5><b><?= $packages->getName() ?></b></h5>
                                                <p><?= LangManager::translate("core.Package.descriptionNotAvailable") ?></p>
                                                <small
                                                    class="align-items-end"><?= LangManager::translate("core.Package.author") ?> <?= $packages->getAuthor() ?></small>
                                            </div>
                                            <div>
                                                <button type="button" data-bs-toggle="modal"
                                                        data-bs-target="#modal-<?= $packages->getName() ?>"
                                                        class="btn btn-sm btn-primary"><?= LangManager::translate("core.Package.details") ?></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="position-absolute"
                                         style="transform: rotate(-45deg); left: -3em; top: 4em; margin: 0; z-index: 50">
                                        <div class="alert-light-warning color-warning text-center px-5"
                                             style="opacity: .85;">
                                            <?= LangManager::translate("core.Package.notVerified") ?>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-around px-2 py-1">
                                            <a class="btn btn-sm btn-danger" type="button" data-bs-toggle="modal"
                                               data-bs-target="#delete-<?= $packages->getName() ?>">
                                                <?= LangManager::translate("core.Package.delete") ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Details modal -->
                            <div class="modal fade text-left w-100" id="modal-<?= $packages->getName() ?>" tabindex="-1"
                                 role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl"
                                     role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title"><?= $packages->getName() ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12 col-lg-6" style="height:20rem">
                                                    <img style="height: 100%; width: 100%;"
                                                         src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Default/local-theme.jpg"
                                                         alt="img">
                                                </div>
                                                <div class="col-12 col-lg-6 position-relative">
                                                    <p class="">
                                                        <b><?= LangManager::translate("core.Package.description") ?></b>
                                                    </p>
                                                    <p><?= LangManager::translate("core.Package.descriptionNotAvailable") ?></p>
                                                    <hr>
                                                    <p class="small">
                                                        <?= LangManager::translate("core.Package.author") ?><i><b><a
                                                                    href=""
                                                                    target="_blank"><?= $packages->getAuthor() ?>
                                                        </i></a></b></i>
                                                    </p>
                                                    <p class="small">
                                                        <?= LangManager::translate("core.Package.version") ?>
                                                        <i><b><?= $packages->getVersion() ?></b></i><br>

                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary"
                                                    data-bs-dismiss="modal"><?= LangManager::translate("core.Package.close") ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Delete modal -->
                            <div class="modal fade text-left" id="delete-<?= $packages->getName() ?>" tabindex="-1"
                                 role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <h5 class="modal-title white"
                                                id="myModalLabel160"><?= LangManager::translate("core.Package.removeTitle") ?> <?= $packages->getName() ?></h5>
                                        </div>
                                        <div class="modal-body text-left">
                                            <p><?= LangManager::translate("core.Package.removeText") ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light-secondary"
                                                    data-bs-dismiss="modal">
                                                <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                            </button>
                                            <a href="" class="btn btn-danger">
                                                <span
                                                    class=""><?= LangManager::translate("core.Package.delete") ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <!------------------------------------
                        -----Listage des packages API installé---
                        -------------------------------------->
                        <?php foreach ($packagesList

                                       as $packages): ?>
                            <?php if (PackageController::isInstalled($packages['name'])): ?>
                                <?php $localPackage = PackageController::getPackage($packages['name']); ?>
                                <div class="col-12 col-lg-6 mb-4">
                                    <div class="card-in-card" style="overflow: hidden;">
                                        <div class="d-flex card-body">
                                            <img class="rounded-3" style="height: 180px; width: 180px;"
                                                 src="<?= $packages["icon"] ?>" alt="img">

                                            <div class="d-flex justify-content-between px-2 py-2 w-100">
                                                <div>
                                                    <h5><b><?= $packages['name'] ?></b></h5>
                                                    <p><?= $packages['description'] ?></p>
                                                    <small
                                                        class="align-items-end"><?= LangManager::translate("core.Package.author") ?> <?= $packages['author_pseudo'] ?></small>
                                                </div>
                                                <div>
                                                    <?php if ($localPackage->getVersion() !== $packages['version_name']): ?>
                                                        <a class="btn btn-sm btn-warning" type="button"
                                                           href="packages/update/<?= $packages['id'] ?>/<?= $localPackage->getVersion() ?>/<?= $localPackage->getName() ?>">
                                                            <?= LangManager::translate("core.Package.update") ?>
                                                        </a>
                                                    <?php else: ?>
                                                        <button type="button" data-bs-toggle="modal"
                                                                data-bs-target="#modal-<?= $packages['name'] ?>"
                                                                class="btn btn-sm btn-primary"><?= LangManager::translate("core.Package.details") ?></button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if ($localPackage->getVersion() !== $packages['version_name']): ?>
                                            <div class="position-absolute"
                                                 style="transform: rotate(-45deg); left: -4em; top: 5em; margin: 0; z-index: 50">
                                                <div class="alert-light-warning color-warning text-center px-5"
                                                     style="opacity: .85;">
                                                    <?= LangManager::translate("core.Theme.update") ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        <hr>
                                        <div class="" style="font-size: small">
                                            <div class="d-flex justify-content-between px-2 py-1">
                                                <div>
                                                    <!--TODO : rating sys-->
                                                    <p><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i> (0)</p>
                                                    <p>Utilisé par <b><?= $packages['downloads'] ?></b> utilisateurs</p>
                                                </div>
                                                <div class="text-end">
                                                    <p>Sortie le <?= CoreController::formatDate($packages['date_release'])?></p>
                                                    <p>Compatible avec <b><?= $packages['version_cmw'] ?></b></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!--Details modal -->
                                <div class="modal fade text-left w-100" id="modal-<?= $packages['name'] ?>"
                                     tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl"
                                         role="document">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex justify-content-between">
                                                <h4 class="modal-title"><?= $packages['name'] ?></h4>
                                                <a class="btn btn-sm btn-danger" type="button" data-bs-toggle="modal"
                                                   data-bs-target="#delete-<?= $packages['name'] ?>">
                                                    <?= LangManager::translate("core.Package.delete") ?>
                                                </a>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12 col-lg-6" style="height:20rem">
                                                        <img style="height: 100%; width: 100%;"
                                                             src="<?= $packages["icon"] ?>" alt="img">
                                                    </div>
                                                    <div class="col-12 col-lg-6 position-relative">
                                                        <p class="">
                                                            <b><?= LangManager::translate("core.Package.description") ?></b>
                                                        </p>
                                                        <p><?= $packages['description'] ?></p>
                                                        <hr>
                                                        <p class="small">
                                                            <?= LangManager::translate("core.Package.author") ?>
                                                            <i>
                                                                <b>
                                                                    <a href="" target="_blank">
                                                                        <?= $packages['author_pseudo'] ?>
                                                                    </a>
                                                                </b>
                                                            </i>
                                                        </p>
                                                        <p class="small">
                                                            <?= LangManager::translate("core.Package.version") ?>
                                                            <i>
                                                                <b>
                                                                    <?= $localPackage->getVersion() ?>
                                                                </b>
                                                            </i>
                                                        </p>

                                                        <?php if ($localPackage->getVersion() !== $packages['version_name']): ?>
                                                            <p class="small">
                                                                <?= LangManager::translate('core.Package.versionDistant') ?>
                                                                :

                                                                <i>
                                                                    <b><?= $packages['version_name'] ?></b>
                                                                </i>
                                                                <br>
                                                            </p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary"
                                                        data-bs-dismiss="modal"><?= LangManager::translate("core.Package.close") ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--Delete modal -->
                                <div class="modal fade text-left" id="delete-<?= $packages['name'] ?>" tabindex="-1"
                                     role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
                                         role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title white"
                                                    id="myModalLabel160"><?= LangManager::translate("core.Package.removeTitle") ?> <?= $packages['name'] ?></h5>
                                            </div>
                                            <div class="modal-body text-left">
                                                <p><?= LangManager::translate("core.Package.removeText") ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light-secondary"
                                                        data-bs-dismiss="modal">
                                                    <span
                                                        class=""><?= LangManager::translate("core.btn.close") ?></span>
                                                </button>
                                                <a href="" class="btn btn-danger">
                                                    <span
                                                        class=""><?= LangManager::translate("core.Package.delete") ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>


                <div class="tab-pane fade py-2" id="setting2" role="tabpanel" aria-labelledby="setting2-tab">
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
                                                        class="align-items-end"><?= LangManager::translate("core.Package.author") ?> <?= $apiPackages['author_pseudo'] ?></small>
                                                </div>
                                                <div>
                                                    <button type="button" data-bs-toggle="modal"
                                                            data-bs-target="#modal-<?= $apiPackages['id'] ?>"
                                                            class="btn btn-sm btn-primary"><?= LangManager::translate("core.Package.details") ?></button>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="" style="font-size: small">
                                            <div class="d-flex justify-content-between px-2 py-1">
                                                <div>
                                                    <!--TODO : rating sys-->
                                                    <p><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i><i class="fa-regular fa-star"></i> (0)</p>
                                                    <p>Utilisé par <b><?= $packages['downloads'] ?></b> utilisateurs</p>
                                                </div>
                                                <div class="text-end">
                                                    <p>Sortie le <?= CoreController::formatDate($packages['date_release'])?></p>
                                                    <p>Compatible avec <b><?= $packages['version_cmw'] ?></b></p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center pb-2">
                                            <a href="packages/install/<?= $apiPackages['id'] ?>"
                                               class="btn btn-sm btn-primary"><i
                                                    class="fa-solid fa-download"></i> <?= LangManager::translate("core.Package.install") ?>
                                            </a>
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
                                                    <a href="packages/install/<?= $apiPackages['id'] ?>"
                                                       class="btn btn-sm btn-primary"><i
                                                            class="fa-solid fa-download"></i> <?= LangManager::translate("core.Package.install") ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12 col-lg-6 mb-3" style="height:20rem">
                                                        <img style="height: 100%; width: 100%;"
                                                             src="<?= $apiPackages["icon"] ?>" alt="img">
                                                    </div>
                                                    <div class="col-12 col-lg-6 position-relative">
                                                        <p class="">
                                                            <b><?= LangManager::translate("core.Package.description") ?></b><br><?= $apiPackages['description'] ?>
                                                        </p>
                                                        <hr>
                                                        <p class="small">
                                                            <?= LangManager::translate("core.Package.author") ?>
                                                            <i><b>
                                                                    <a href="" target="_blank">
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
                                                        data-bs-dismiss="modal"><?= LangManager::translate("core.Package.close") ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>