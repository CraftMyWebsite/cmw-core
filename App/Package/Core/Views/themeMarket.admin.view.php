<?php

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Env\EnvManager;
/* @var $currentTheme \CMW\Entity\Core\ThemeEntity */
/* @var $installedThemes \CMW\Entity\Core\ThemeEntity[] */
/* @var $themesList */

$title = LangManager::translate("core.Theme.config.title");
$description = LangManager::translate("core.Theme.config.description"); ?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-shop"></i> <span class="m-lg-auto">Market</span></h3>
</div>

<div class="row">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="setting1-tab" data-bs-toggle="tab" href="#setting1" role="tab"
                       aria-selected="true"><?= LangManager::translate("core.Theme.myThemes") ?></a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="setting2-tab" data-bs-toggle="tab" href="#setting2" role="tab"
                       aria-selected="false"><?= LangManager::translate("core.Theme.market") ?></a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active py-2" id="setting1" role="tabpanel"
                     aria-labelledby="setting1-tab">
                    <div class="row">
                        <!------------------------------------
                        -----Listage des thèmes local ACTIF---
                        -------------------------------------->
                        <?php foreach (ThemeController::getLocalThemes() as $theme): ?>
                            <?php if ($theme->getName() === $currentTheme->getName()): ?>
                            <div class="col-12 col-lg-3 mb-4">
                                <div class="card-in-card">
                                    <div class="d-flex justify-content-between align-items-center px-2 py-2">
                                        <b><?= $theme->getName() ?></b>
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#modal-<?= $theme->getName() ?>" class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.details") ?></button>
                                    </div>
                                    <div class="position-relative">
                                        <!--<div class="alert-light-warning color-warning position-absolute w-100 text-center" style="opacity: .80">
                                            Une mises à jour est disponible !
                                        </div>-->
                                        <?php if ($theme->getName() != "Sampler"): ?>
                                        <div class="alert-light-warning color-warning position-absolute bottom-0 w-100 text-center" style="opacity: .85">
                                            <?= LangManager::translate("core.Theme.notVerified") ?>
                                        </div>
                                        <?php endif; ?>
                                        <img style="height: 200px; width: 100%;" src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Default/local-theme.jpg" alt="img">  
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center px-2 py-1">
                                        <span class="text-success"><?= LangManager::translate("core.Theme.active") ?></span>
                                        <a href="manage" class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.configure") ?></a>
                                    </div>
                                </div>
                            </div>
                            <!--Details modal -->
                            <div class="modal fade text-left w-100" id="modal-<?= $theme->getName() ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title"><?= $theme->getName() ?></h4>
                                            <div class="d-flex justify-content-end mt-auto gap-3">
                                                <a href="manage" class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.configure") ?></a>
                                                <form action="market/regenerate" method="post">
                                                    <?php (new SecurityManager())->insertHiddenToken() ?>
                                                    <div class="button">
                                                        <button type="submit" class="btn btn-warning btn-sm float-left">
                                                            <?= LangManager::translate("core.Theme.reset") ?>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12 col-lg-6" style="height:20rem">
                                                    <img style="height: 100%; width: 100%;" src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Default/local-theme.jpg" alt="img">
                                                </div>
                                                <div class="col-12 col-lg-6 position-relative">
                                                        <p class=""><b><?= LangManager::translate("core.Theme.description") ?></b></p>
                                                        <?php if ($theme->getName() != "Sampler"): ?>
                                                            <p><?= LangManager::translate("core.Theme.descriptionManualInstall") ?></p>
                                                        <?php else: ?>
                                                            <p><?= LangManager::translate("core.Theme.descriptionIsSampler") ?></p>
                                                        <?php endif; ?>
                                                        <hr>
                                                        <p class="small">
                                                            <?= LangManager::translate("core.Theme.author") ?><i><b><a href="" target="_blank"><?= $theme->getAuthor() ?? $theme->getAuthorsFormatted() ?></i></a></b></i>
                                                        </p>
                                                        <p class="small">
                                                            <?= LangManager::translate("core.Theme.themeVersion") ?><i><b><?= $theme->getVersion() ?></b></i><br>
                                                            <?= LangManager::translate("core.Theme.CMWVersion") ?><i><b><?= $theme->getCmwVersion() ?></b></i>
                                                        </p>
                                                </div>
                                            </div>
                                        </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary"data-bs-dismiss="modal"><?= LangManager::translate("core.Theme.close") ?></button>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <!------------------------------------------------
                        -----Listage des thèmes API installé et ACTIF---
                        -------------------------------------------------->
                        <?php foreach ($themesList as $theme): ?>
                            <?php if ($theme['name'] === $currentTheme->getName()): ?>
                                <div class="col-12 col-lg-3 mb-4">
                                    <div class="card-in-card">
                                        <div class="d-flex justify-content-between align-items-center px-2 py-2">
                                            <b><?= $theme['name'] ?></b>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#modal-<?= $theme['id'] ?>" class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.details") ?></button>
                                        </div>
                                        <div class="position-relative">
                                            <!--<div class="alert-light-warning color-warning position-absolute w-100 text-center" style="opacity: .80">
                                                Une mises à jour est disponible !
                                            </div>-->
                                            <img style="height: 200px; width: 100%;" src="<?= PublicAPI::getUrl() . '/' . $theme["icon"] ?>" alt="img">  
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center px-2 py-1">
                                            <span class="text-success"><?= LangManager::translate("core.Theme.active") ?></span>
                                            <a href="manage" class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.configure") ?></a>
                                        </div>
                                    </div>
                                </div>
                                <!--Details modal-->
                                <div class="modal fade text-left w-100" id="modal-<?= $theme['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title"><?= $theme['name'] ?></h4>
                                                <div class="d-flex justify-content-end mt-auto gap-3">
                                                    <a href="manage" class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.configure") ?></a>
                                                    <a href="install/<?= $theme['id'] ?>" class="btn btn-sm btn-danger"><?= LangManager::translate("core.Theme.reset") ?></a>
                                                   <form action="market/regenerate" method="post">
                                                        <?php (new SecurityManager())->insertHiddenToken() ?>
                                                        <div class="button">
                                                            <button type="submit" class="btn btn-warning btn-sm float-left"><?= LangManager::translate("core.Theme.reset") ?></button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12 col-lg-6 mb-3" style="height:20rem">
                                                        <img style="height: 100%; width: 100%;" src="<?= PublicAPI::getUrl() . '/' . $theme["icon"] ?>" alt="img">
                                                    </div>
                                                    <div class="col-12 col-lg-6 position-relative">
                                                        <p class=""><b><?= LangManager::translate("core.Theme.description") ?></b><br><?= $theme['description'] ?></p>
                                                        <hr>
                                                        <p class="small">
                                                            <?= LangManager::translate("core.Theme.author") ?><i><b><a href="" target="_blank"><?= $theme['author_pseudo'] ?></i></a></b></i><br>
                                                            <?= LangManager::translate("core.Theme.downloads") ?><i><b><?= $theme['downloads'] ?></b></i>
                                                        </p>
                                                        <p class="small">
                                                            <?= LangManager::translate("core.Theme.themeVersion") ?><i><b><?= $theme['version_name'] ?></b></i><br>
                                                            <?= LangManager::translate("core.Theme.CMWVersion") ?><i><b><?= $theme['version_cmw'] ?></b></i>
                                                        </p>
                                                        <div class="d-flex gap-3 align-items-center">
                                                            <?php if ($theme['demo']): ?>
                                                            <a class="btn btn-sm btn-primary" href="<?= $theme['demo'] ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> <?= LangManager::translate("core.Theme.demo") ?></a>
                                                            <?php endif; ?>
                                                            <?php if ($theme['code_link']): ?>
                                                                <a class="btn btn-sm btn-primary" href="<?= $theme['code_link'] ?>" target="_blank"><i class="fa-brands fa-github"></i> GitHub</a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary"data-bs-dismiss="modal"><?= LangManager::translate("core.Theme.close") ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <!--------------------------------------
                        -----Listage des thèmes local inactif---
                        ---------------------------------------->
                        <?php foreach (ThemeController::getLocalThemes() as $theme): ?>
                            <?php if ($theme->getName() != $currentTheme->getName()): ?>
                            <div class="col-12 col-lg-3 mb-4">
                                <div class="card-in-card">
                                    <div class="d-flex justify-content-between align-items-center px-2 py-2">
                                        <b><?= $theme->getName() ?></b>
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#modal-<?= $theme->getName() ?>" class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.details") ?></button>
                                    </div>
                                    <div class="position-relative">
                                        <?php if ($theme->getName() != "Sampler"): ?>
                                        <div class="alert-light-warning color-warning position-absolute bottom-0 w-100 text-center" style="opacity: .85">
                                            <?= LangManager::translate("core.Theme.notVerified") ?>
                                        </div>
                                        <?php endif; ?>
                                        <img class="rounded-bottom" style="height: 200px; width: 100%;" src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Default/local-theme.jpg" alt="im">
                                    </div>
                                    <div class="d-flex justify-content-center px-2 py-1">
                                        <form action="" method="post">
                                            <?php (new SecurityManager())->insertHiddenToken() ?>
                                            <input hidden type="text" name="theme"value="<?= $theme->getName() ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Activer</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!--Details modal -->
                            <div class="modal fade text-left w-100" id="modal-<?= $theme->getName() ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title"><?= $theme->getName() ?></h4>
                                            <div class="d-flex justify-content-end px-2 py-1">
                                                <form action="" method="post">
                                                    <?php (new SecurityManager())->insertHiddenToken() ?>
                                                    <input hidden type="text" name="theme"value="<?= $theme->getName() ?>">
                                                        <button type="submit" class="btn btn-sm btn-success">Activer</button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-12 col-lg-6" style="height:20rem">
                                                    <img style="height: 100%; width: 100%;" src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Default/local-theme.jpg" alt="img">
                                                </div>
                                                <div class="col-12 col-lg-6 position-relative">
                                                        <p class=""><b>Déscription : </b></p>
                                                        <?php if ($theme->getName() != "Sampler"): ?>
                                                            <p>Ce thème est installé manuellement, il n'est pas enregistrer auprès de CraftMyWebsite.<br>Utilisez ce thème en conaissance de cause.<br>Si vous développez ce thème pour le publier ensuite sur le Market de CraftMyWebsite ne tenez pas compte de ce message.</p>
                                                        <?php else: ?>
                                                            <p>Sampler est le thème par défaut fournis avec CraftMyWebsite.</p>
                                                        <?php endif; ?>
                                                        <hr>
                                                        <p class="small">
                                                            Autheur : <i><b><a href="" target="_blank"><?= $theme->getAuthor() ?? $theme->getAuthorsFormatted() ?></i></a></b></i>
                                                        </p>
                                                        <p class="small">
                                                            Version du thème : <i><b><?= $theme->getVersion() ?></b></i><br>
                                                            Version CMW recommandée : <i><b><?= $theme->getCmwVersion() ?></b></i>
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
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <!------------------------------------------------
                        -----Listage des thèmes API installé et inactif---
                        -------------------------------------------------->
                        <?php foreach ($themesList as $theme): ?>
                            <?php if (ThemeController::isThemeInstalled($theme['name']) && $theme['name'] !== $currentTheme->getName()): ?>
                                <div class="col-12 col-lg-3 mb-4">
                                    <div class="card-in-card">
                                        <div class="d-flex justify-content-between align-items-center px-2 py-2">
                                            <b><?= $theme['name'] ?></b>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#modal-<?= $theme['id'] ?>" class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.details") ?></button>
                                        </div>
                                        <div class="position-relative">
                                            <!--<div class="alert-light-warning color-warning position-absolute w-100 text-center" style="opacity: .80">
                                                Une mises à jour est disponible !
                                            </div>-->
                                            <img style="height: 200px; width: 100%;" src="<?= PublicAPI::getUrl() . '/' . $theme["icon"] ?>" alt="img">  
                                        </div>
                                        <div class="d-flex justify-content-center px-2 py-1">
                                            <form action="" method="post">
                                            <?php (new SecurityManager())->insertHiddenToken() ?>
                                            <input hidden type="text" name="theme"value="<?= $theme['name'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Activer</button>
                                        </form>
                                        </div>
                                    </div>
                                </div>
                                <!--Details modal-->
                                <div class="modal fade text-left w-100" id="modal-<?= $theme['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title"><?= $theme['name'] ?></h4>
                                                <div class="d-flex justify-content-end mt-auto gap-3">
                                                    <a href="install/<?= $theme['id'] ?>" class="btn btn-sm btn-danger">Réinstaller</a>
                                                    <form action="" method="post">
                                                        <?php (new SecurityManager())->insertHiddenToken() ?>
                                                        <input hidden type="text" name="theme"
                                                               value="<?= $theme['name'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-success">Activer
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12 col-lg-6 mb-3" style="height:20rem">
                                                        <img style="height: 100%; width: 100%;" src="<?= PublicAPI::getUrl() . '/' . $theme["icon"] ?>" alt="img">
                                                    </div>
                                                    <div class="col-12 col-lg-6 position-relative">
                                                        <p class=""><b>Déscription : </b><br><?= $theme['description'] ?></p>
                                                        <hr>
                                                        <p class="small">
                                                            Autheur : <i><b><a href="" target="_blank"><?= $theme['author_pseudo'] ?></i></a></b></i><br>
                                                            Téléchargements : <i><b><?= $theme['downloads'] ?></b></i>
                                                        </p>
                                                        <p class="small">
                                                            Version du thème : <i><b><?= $theme['version_name'] ?></b></i><br>
                                                            Version CMW recommandée : <i><b><?= $theme['version_cmw'] ?></b></i>
                                                        </p>
                                                        <div class="d-flex gap-3 align-items-center">
                                                            <?php if ($theme['demo']): ?>
                                                            <a class="btn btn-sm btn-primary" href="<?= $theme['demo'] ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Démo</a>
                                                            <?php endif; ?>
                                                            <?php if ($theme['code_link']): ?>
                                                                <a class="btn btn-sm btn-primary" href="<?= $theme['code_link'] ?>" target="_blank"><i class="fa-brands fa-github"></i> GitHub</a>
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
                <div class="tab-pane fade py-2" id="setting2" role="tabpanel" aria-labelledby="setting2-tab">
                    <div class="row">
                        <!----------------------------------------
                        -----Listage des thèmes API non nstallé---
                        ------------------------------------------>
                        <?php foreach ($themesList as $theme): ?>
                            <?php if (!ThemeController::isThemeInstalled($theme['name'])): ?>
                                <div class="col-12 col-lg-3 mb-4">
                                    <div class="card-in-card">
                                        <div class="d-flex justify-content-between align-items-center px-2 py-2">
                                            <b><?= $theme['name'] ?></b>
                                            <button type="button" data-bs-toggle="modal" data-bs-target="#modal-<?= $theme['id'] ?>" class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.details") ?></button>
                                        </div>
                                        <div class="position-relative">
                                            <!--<div class="alert-light-warning color-warning position-absolute w-100 text-center" style="opacity: .80">
                                                Une mises à jour est disponible !
                                            </div>-->
                                            <img style="height: 200px; width: 100%;" src="<?= PublicAPI::getUrl() . '/' . $theme["icon"] ?>" alt="img">  
                                        </div>
                                        <div class="d-flex justify-content-center px-2 py-1">
                                            <a href="install/<?= $theme['id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-download"></i> Installer</a>
                                        </div>
                                    </div>
                                </div>
                                <!--Details modal-->
                                <div class="modal fade text-left w-100" id="modal-<?= $theme['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title"><?= $theme['name'] ?></h4>
                                                <div class="d-flex justify-content-end mt-auto gap-3">
                                                    <a href="install/<?= $theme['id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-download"></i> Installer</a>
                                                </div>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12 col-lg-6 mb-3" style="height:20rem">
                                                        <img style="height: 100%; width: 100%;" src="<?= PublicAPI::getUrl() . '/' . $theme["icon"] ?>" alt="img">
                                                    </div>
                                                    <div class="col-12 col-lg-6 position-relative">
                                                        <p class=""><b>Déscription : </b><br><?= $theme['description'] ?></p>
                                                        <hr>
                                                        <p class="small">
                                                            Autheur : <i><b><a href="" target="_blank"><?= $theme['author_pseudo'] ?></i></a></b></i><br>
                                                            Téléchargements : <i><b><?= $theme['downloads'] ?></b></i>
                                                        </p>
                                                        <p class="small">
                                                            Version du thème : <i><b><?= $theme['version_name'] ?></b></i><br>
                                                            Version CMW recommandée : <i><b><?= $theme['version_cmw'] ?></b></i>
                                                        </p>
                                                        <div class="d-flex gap-3 align-items-center">
                                                            <?php if ($theme['demo']): ?>
                                                            <a class="btn btn-sm btn-primary" href="<?= $theme['demo'] ?>" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Démo</a>
                                                            <?php endif; ?>
                                                            <?php if ($theme['code_link']): ?>
                                                                <a class="btn btn-sm btn-primary" href="<?= $theme['code_link'] ?>" target="_blank"><i class="fa-brands fa-github"></i> GitHub</a>
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

<!--------------------
-----MODAL de reset---
---------------------->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle">Verification</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    Attention, ceci va réinitialiser tout les paramètres par defaut de votre thème, êtes vous sûr de
                    vouloir continuer ?
                </p>
            </div>
            <div class="modal-footer">
                <form action="market/regenerate" method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="button">
                        <button type="button" class="btn btn-light-primary" data-bs-dismiss="modal">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-danger float-left">
                            Confirmer
                        </button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>