<?php


use CMW\Controller\Core\PackageController;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;

/* @var \CMW\Entity\Core\PackageEntity[] $installedPackages*/
/* @var PackageController[] $packagesList */

$title = LangManager::translate("core.Theme.config.title");
$description = LangManager::translate("core.Theme.config.description"); ?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-puzzle-piece"></i> <span class="m-lg-auto">Packages</span></h3>
</div>

<div class="row">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="setting1-tab" data-bs-toggle="tab" href="#setting1" role="tab"
                       aria-selected="true">Mes packages</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="setting2-tab" data-bs-toggle="tab" href="#setting2" role="tab"
                       aria-selected="false">Market</a>
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
                                <div class="col-12 col-lg-3 mb-4">
                                    <div class="card-in-card">
                                        <div class="d-flex justify-content-between align-items-center px-2 py-2">
                                            <b><?= $packages->getName() ?></b>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#modal-<?= $packages->getName() ?>" class="btn btn-sm btn-primary">Détails</button>
                                        </div>
                                        <div class="position-relative">
                                            <!--<div class="alert-light-warning color-warning position-absolute w-100 text-center" style="opacity: .80">
                                                Une mises à jour est disponible !
                                            </div>-->
                                            <div class="alert-light-warning color-warning position-absolute bottom-0 w-100 text-center" style="opacity: .85">
                                                Non verifié par CMW.
                                            </div>
                                            <img class="rounded-3 " style="height: 200px; width: 100%;" src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Default/local-theme.jpg" alt="img">
                                        </div>
                                        <div class="d-flex justify-content-around px-2 py-1">
                                            <a class="btn btn-sm btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#delete-<?= $packages->getName() ?>">
                                                Supprimer
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <!--Details modal -->
                                <div class="modal fade text-left w-100" id="modal-<?= $packages->getName() ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title"><?= $packages->getName() ?></h4>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12 col-lg-6" style="height:20rem">
                                                        <img style="height: 100%; width: 100%;" src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Default/local-theme.jpg" alt="img">
                                                    </div>
                                                    <div class="col-12 col-lg-6 position-relative">
                                                        <p class=""><b>Déscription : </b></p>
                                                            <p>Les déscription ne sont pas disponnible pour les packages local</p>
                                                        <hr>
                                                        <p class="small">
                                                            Autheur : <i><b><a href="" target="_blank"><?= $packages->getAuthor() ?? $packages->getAuthorsFormatted() ?></i></a></b></i>
                                                        </p>
                                                        <p class="small">
                                                            Version du thème : <i><b><?= $packages->getVersion() ?></b></i><br>

                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary"data-bs-dismiss="modal">Fermer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!--Delete modal -->
                            <div class="modal fade text-left" id="delete-<?= $packages->getName() ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <h5 class="modal-title white" id="myModalLabel160">Voulez-vous supprimer <?= $packages->getName() ?></h5>
                                        </div>
                                        <div class="modal-body text-left">
                                            <p>La suppression de ce package est définitive.<br>Voulez-vous continuer ?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                            </button>
                                            <a href="" class="btn btn-danger">
                                                <span class="">Supprimer</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <!------------------------------------
                        -----Listage des packages API installé---
                        -------------------------------------->
                        <?php foreach ($packagesList as $packages): ?>
                            <div class="col-12 col-lg-3 mb-4">
                                <div class="card-in-card">
                                    <div class="d-flex justify-content-between align-items-center px-2 py-2">
                                        <b><?= $packages['name'] ?></b>
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#modal-<?= $packages['name'] ?>" class="btn btn-sm btn-primary">Détails</button>
                                    </div>
                                    <div class="position-relative">
                                        <!--<div class="alert-light-warning color-warning position-absolute w-100 text-center" style="opacity: .80">
                                            Une mises à jour est disponible !
                                        </div>-->
                                        <img class="rounded-3 " style="height: 200px; width: 100%;" src="<?= PublicAPI::getUrl() . '/' . $packages["icon"] ?>" alt="img">
                                    </div>
                                    <div class="d-flex justify-content-around px-2 py-1">
                                        <a class="btn btn-sm btn-danger" type="button" data-bs-toggle="modal" data-bs-target="#delete-<?= $packages['name'] ?>">
                                            Supprimer
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!--Details modal -->
                            <div class="modal fade text-left w-100" id="modal-<?= $packages['name'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title"><?= $packages['name'] ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12 col-lg-6" style="height:20rem">
                                                    <img style="height: 100%; width: 100%;" src="<?= PublicAPI::getUrl() . '/' . $packages["icon"] ?>" alt="img">
                                                </div>
                                                <div class="col-12 col-lg-6 position-relative">
                                                    <p class=""><b>Déscription : </b></p>
                                                    <p><?= $packages['description'] ?></p>
                                                    <hr>
                                                    <p class="small">
                                                        Autheur : <i><b><a href="" target="_blank"><?= $packages['author_pseudo'] ?></i></a></b></i>
                                                    </p>
                                                    <p class="small">
                                                        Version du thème : <i><b><?= $packages['version_name'] ?></b></i><br>

                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary"data-bs-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--Delete modal -->
                            <div class="modal fade text-left" id="delete-<?= $packages['name'] ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger">
                                            <h5 class="modal-title white" id="myModalLabel160">Voulez-vous supprimer <?= $packages['name'] ?></h5>
                                        </div>
                                        <div class="modal-body text-left">
                                            <p>La suppression de ce package est définitive.<br>Voulez-vous continuer ?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                                <span class=""><?= LangManager::translate("core.btn.close") ?></span>
                                            </button>
                                            <a href="" class="btn btn-danger">
                                                <span class="">Supprimer</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>


                <div class="tab-pane fade py-2" id="setting2" role="tabpanel" aria-labelledby="setting2-tab">
                    <div class="row">
                        <!----------------------------------------
                        -----Listage des thèmes API non nstallé---
                        ------------------------------------------>
                        <?php foreach ($packagesList as $apiPackages): ?>
                            <?php if (!PackageController::isInstalled($apiPackages['name'])): ?>
                                <div class="col-12 col-lg-3 mb-4">
                                    <div class="card-in-card">
                                        <div class="d-flex justify-content-between align-items-center px-2 py-2">
                                            <b><?= $apiPackages['name'] ?></b>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#modal-<?= $apiPackages['id'] ?>" class="btn btn-sm btn-primary">Détails</button>
                                        </div>
                                        <div class="position-relative">
                                            <!--<div class="alert-light-warning color-warning position-absolute w-100 text-center" style="opacity: .80">
                                                Une mises à jour est disponible !
                                            </div>-->
                                            <img style="height: 200px; width: 100%;" src="<?= PublicAPI::getUrl() . '/' . $apiPackages["icon"] ?>" alt="img">
                                        </div>
                                        <div class="d-flex justify-content-center px-2 py-1">
                                            <a href="install/<?= $apiPackages['id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-download"></i> Installer</a>
                                        </div>
                                    </div>
                                </div>
                                <!--Details modal-->
                                <div class="modal fade text-left w-100" id="modal-<?= $apiPackages['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title"><?= $apiPackages['name'] ?></h4>
                                                <div class="d-flex justify-content-end mt-auto gap-3">
                                                    <a href="install/<?= $apiPackages['id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-download"></i> Installer</a>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12 col-lg-6 mb-3" style="height:20rem">
                                                        <img style="height: 100%; width: 100%;" src="<?= PublicAPI::getUrl() . '/' . $apiPackages["icon"] ?>" alt="img">
                                                    </div>
                                                    <div class="col-12 col-lg-6 position-relative">
                                                        <p class=""><b>Déscription : </b><br><?= $apiPackages['description'] ?></p>
                                                        <hr>
                                                        <p class="small">
                                                            Autheur : <i><b><a href="" target="_blank"><?= $apiPackages['author_pseudo'] ?></i></a></b></i><br>
                                                            Téléchargements : <i><b><?= $apiPackages['downloads'] ?></b></i>
                                                        </p>
                                                        <p class="small">
                                                            Version du thème : <i><b><?= $apiPackages['version_name'] ?></b></i><br>
                                                            Version CMW recommandée : <i><b><?= $apiPackages['version_cmw'] ?></b></i>
                                                        </p>
                                                        <div class="d-flex gap-3 align-items-center">
                                                            <?php if ($apiPackages['demo']): ?>
                                                                <a class="btn btn-sm btn-primary" href="<?= $apiPackages['demo'] ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Démo</a>
                                                            <?php endif; ?>
                                                            <?php if ($apiPackages['code_link']): ?>
                                                                <a class="btn btn-sm btn-primary" href="<?= $apiPackages['code_link'] ?>" target="_blank"><i class="fa-brands fa-github"></i> GitHub</a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary"data-bs-dismiss="modal">Fermer</button>
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