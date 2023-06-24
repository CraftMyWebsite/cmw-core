<?php

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var $currentTheme \CMW\Entity\Core\ThemeEntity */
/* @var $installedThemes \CMW\Entity\Core\ThemeEntity[] */
/* @var $themesList */

$title = LangManager::translate("core.Theme.config.title");
$description = LangManager::translate("core.Theme.config.description"); ?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-shop"></i> <span
            class="m-lg-auto">Mes thèmes</span></h3>
</div>

<div class="row">

    <!------------------------------------
    -----Listage des thèmes local ACTIF---
    -------------------------------------->
    <?php foreach (ThemeController::getLocalThemes() as $theme): ?>
        <?php if ($theme->getName() === $currentTheme->getName()): ?>
            <div class="col-12 col-lg-3 mb-4">
                <div class="card" style="overflow: hidden;">
                    <div class="d-flex justify-content-between align-items-center px-2 py-2">
                        <b><?= $theme->getName() ?></b>
                        <button type="button" data-bs-toggle="modal"
                                data-bs-target="#modal-<?= $theme->getName() ?>"
                                class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.details") ?></button>
                    </div>
                    <div class="position-relative">
                        <?php if ($theme->getName() !== "Sampler"): ?>
                            <div class="alert-light-warning color-warning position-absolute bottom-0 w-100 text-center"
                                 style="opacity: .85">
                                <?= LangManager::translate("core.Theme.notVerified") ?>
                            </div>
                        <?php endif; ?>
                        <img style="height: 200px; width: 100%;"
                             src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Default/local-theme.jpg"
                             alt="img">
                    </div>
                    <div class="d-flex justify-content-center align-items-center px-2 py-1">
                        <a href="manage"
                           class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.configure") ?></a>
                    </div>
                    <div class="position-absolute"
                         style="transform: rotate(-45deg); left: -3em; top: 3em; margin: 0; z-index: 50">
                        <div class="alert-light-success text-center"
                             style="opacity: .85; padding-left: 4.5rem; padding-right: 4.5rem ">
                            <?= LangManager::translate("core.Theme.active") ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--Details modal -->
            <div class="modal fade text-left w-100" id="modal-<?= $theme->getName() ?>"
                 tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl"
                     role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><?= $theme->getName() ?></h4>
                            <div class="d-flex justify-content-end mt-auto gap-3">
                                <a href="manage"
                                   class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.configure") ?></a>
                                <form action="market/regenerate" method="post">
                                    <?php (new SecurityManager())->insertHiddenToken() ?>
                                    <div class="button">
                                        <button type="submit"
                                                class="btn btn-warning btn-sm float-left">
                                            <?= LangManager::translate("core.Theme.reset") ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
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
                                        <b><?= LangManager::translate("core.Theme.description") ?></b>
                                    </p>
                                    <?php if ($theme->getName() !== "Sampler"): ?>
                                        <p><?= LangManager::translate("core.Theme.descriptionManualInstall") ?></p>
                                    <?php else: ?>
                                        <p><?= LangManager::translate("core.Theme.descriptionIsSampler") ?></p>
                                    <?php endif; ?>
                                    <hr>
                                    <p class="small">
                                        <?= LangManager::translate("core.Theme.author") ?><i><b><a
                                                    href=""
                                                    target="_blank"><?= $theme->getAuthor() ?? $theme->getAuthorsFormatted() ?>
                                        </i></a></b></i>
                                    </p>
                                    <p class="small">
                                        <?= LangManager::translate("core.Theme.themeVersion") ?>
                                        <i><b><?= $theme->getVersion() ?></b></i><br>
                                        <?= LangManager::translate("core.Theme.CMWVersion") ?>
                                        <i><b><?= $theme->getCmwVersion() ?></b></i>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"
                                    data-bs-dismiss="modal"><?= LangManager::translate("core.Theme.close") ?></button>
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
            <?php $localTheme = ThemeController::getTheme($theme['name']); ?>
            <div class="col-12 col-lg-3 mb-4">
                <div class="card" style="overflow: hidden;">
                    <div class="d-flex justify-content-between align-items-center px-2 py-2">
                        <b><?= $theme['name'] ?></b>
                        <button type="button" data-bs-toggle="modal"
                                data-bs-target="#modal-<?= $theme['id'] ?>"
                                class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.details") ?></button>
                    </div>
                    <div class="position-relative">
                        <img style="height: 200px; width: 100%;" src="<?= $theme["icon"] ?>"
                             alt="Icon <?= $theme['name'] ?>">
                    </div>
                    <div class="d-flex justify-content-around align-items-center px-2 py-1">
                        <?php if ($localTheme->getVersion() !== $theme['version_name']): ?>
                            <a class="btn btn-sm btn-warning" type="button"
                               href="update/<?= $theme['id'] ?>/<?= $localTheme->getVersion() ?>/<?= $localTheme->getName() ?>">
                                <?= LangManager::translate("core.Package.update") ?>
                            </a>
                        <?php endif; ?>
                            <a href="manage"
                               class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.configure") ?></a>

                    </div>
                    <div class="position-absolute"
                         style="transform: rotate(-45deg); left: -3em; top: 3em; margin: 0; z-index: 50">
                        <div class="alert-light-success text-center"
                             style="opacity: .85; padding-left: 4.5rem; padding-right: 4.5rem ">
                            <?= LangManager::translate("core.Theme.active") ?>
                        </div>
                    </div>
                    <?php if ($localTheme->getVersion() !== $theme['version_name']): ?>
                        <div class="position-absolute"
                             style="transform: rotate(-45deg); left: -4em; top: 5em; margin: 0; z-index: 50">
                            <div class="alert-light-warning color-warning text-center"
                                 style="opacity: .85;padding-left: 4.5rem; padding-right: 4.5rem ">
                                <?= LangManager::translate("core.Theme.update") ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!--Details modal-->
            <div class="modal fade text-left w-100" id="modal-<?= $theme['id'] ?>" tabindex="-1"
                 role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl"
                     role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><?= $theme['name'] ?></h4>
                            <div class="d-flex justify-content-end mt-auto gap-3">
                                <a href="manage"
                                   class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.configure") ?></a>
                                <a href="install/<?= $theme['id'] ?>"
                                   class="btn btn-sm btn-danger"><?= LangManager::translate("core.Theme.reinstall") ?></a>
                                <form action="market/regenerate" method="post">
                                    <?php (new SecurityManager())->insertHiddenToken() ?>
                                    <div class="button">
                                        <button type="submit"
                                                class="btn btn-warning btn-sm float-left"><?= LangManager::translate("core.Theme.reset") ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 col-lg-6 mb-3" style="height:20rem">
                                    <img style="height: 100%; width: 100%;"
                                         src="<?= $theme["icon"] ?>"
                                         alt="Icon <?= $theme['name'] ?>">
                                </div>
                                <div class="col-12 col-lg-6 position-relative">
                                    <p class="">
                                        <b><?= LangManager::translate("core.Theme.description") ?></b><br><?= $theme['description'] ?>
                                    </p>
                                    <hr>
                                    <p class="small">
                                        <?= LangManager::translate("core.Theme.author") ?><i><b><a
                                                    href=""
                                                    target="_blank"><?= $theme['author_pseudo'] ?>
                                        </i></a></b></i><br>
                                        <?= LangManager::translate("core.Theme.downloads") ?>
                                        <i><b><?= $theme['downloads'] ?></b></i>
                                    </p>
                                    <p class="small">
                                        <?= LangManager::translate("core.Theme.themeVersion") ?>
                                        <i><b><?= $theme['version_name'] ?></b></i><br>
                                        <?= LangManager::translate("core.Theme.CMWVersion") ?>
                                        <i><b><?= $theme['version_cmw'] ?></b></i>
                                    </p>
                                    <div class="d-flex gap-3 align-items-center">
                                        <?php if ($theme['demo']): ?>
                                            <a class="btn btn-sm btn-primary"
                                               href="<?= $theme['demo'] ?>" target="_blank"><i
                                                    class="fa-solid fa-arrow-up-right-from-square"></i> <?= LangManager::translate("core.Theme.demo") ?>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($theme['code_link']): ?>
                                            <a class="btn btn-sm btn-primary"
                                               href="<?= $theme['code_link'] ?>" target="_blank"><i
                                                    class="fa-brands fa-github"></i> GitHub</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"
                                    data-bs-dismiss="modal"><?= LangManager::translate("core.Theme.close") ?></button>
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
                <div class="card">
                    <div class="d-flex justify-content-between align-items-center px-2 py-2">
                        <b><?= $theme->getName() ?></b>
                        <button type="button" data-bs-toggle="modal"
                                data-bs-target="#modal-<?= $theme->getName() ?>"
                                class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.details") ?></button>
                    </div>
                    <div class="position-relative">
                        <?php if ($theme->getName() !== "Sampler"): ?>
                            <div class="alert-light-warning color-warning position-absolute bottom-0 w-100 text-center"
                                 style="opacity: .85">
                                <?= LangManager::translate("core.Theme.notVerified") ?>
                            </div>
                        <?php endif; ?>
                        <img class="rounded-bottom" style="height: 200px; width: 100%;"
                             src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Images/Default/local-theme.jpg"
                             alt="im">
                    </div>
                    <div class="d-flex justify-content-center px-2 py-1">
                        <form action="" method="post">
                            <?php (new SecurityManager())->insertHiddenToken() ?>
                            <input hidden type="text" name="theme" value="<?= $theme->getName() ?>">
                            <button type="submit"
                                    class="btn btn-sm btn-success"><?= LangManager::translate("core.Theme.activate") ?></button>
                        </form>
                    </div>
                </div>
            </div>
            <!--Details modal -->
            <div class="modal fade text-left w-100" id="modal-<?= $theme->getName() ?>"
                 tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl"
                     role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><?= $theme->getName() ?></h4>
                            <div class="d-flex justify-content-end px-2 py-1">
                                <form action="" method="post">
                                    <?php (new SecurityManager())->insertHiddenToken() ?>
                                    <input hidden type="text" name="theme"
                                           value="<?= $theme->getName() ?>">
                                    <button type="submit"
                                            class="btn btn-sm btn-success"><?= LangManager::translate("core.Theme.activate") ?></button>
                                </form>
                            </div>
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
                                        <b><?= LangManager::translate("core.Theme.description") ?></b>
                                    </p>
                                    <?php if ($theme->getName() !== "Sampler"): ?>
                                        <p><?= LangManager::translate("core.Theme.descriptionManualInstall") ?></p>
                                    <?php else: ?>
                                        <p><?= LangManager::translate("core.Theme.descriptionIsSampler") ?></p>
                                    <?php endif; ?>
                                    <hr>
                                    <p class="small">
                                        <?= LangManager::translate("core.Theme.author") ?><i><b><a
                                                    href=""
                                                    target="_blank"><?= $theme->getAuthor() ?? $theme->getAuthorsFormatted() ?>
                                        </i></a></b></i>
                                    </p>
                                    <p class="small">
                                        <?= LangManager::translate("core.Theme.themeVersion") ?>
                                        <i><b><?= $theme->getVersion() ?></b></i><br>
                                        <?= LangManager::translate("core.Theme.CMWVersion") ?>
                                        <i><b><?= $theme->getCmwVersion() ?></b></i>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"
                                    data-bs-dismiss="modal"><?= LangManager::translate("core.Theme.close") ?></button>
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
            <?php $localTheme = ThemeController::getTheme($theme['name']); ?>
            <div class="col-12 col-lg-3 mb-4">
                <div class="card" style="overflow: hidden;">
                    <div class="d-flex justify-content-between align-items-center px-2 py-2">
                        <b><?= $theme['name'] ?></b>
                        <button type="button" data-bs-toggle="modal"
                                data-bs-target="#modal-<?= $theme['id'] ?>"
                                class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.details") ?></button>
                    </div>
                    <div class="position-relative">
                        <img style="height: 200px; width: 100%;" src="<?= $theme["icon"] ?>"
                             alt="Icon <?= $theme['name'] ?>">
                    </div>
                    <div class="d-flex justify-content-around px-2 py-1">
                        <?php if ($localTheme->getVersion() !== $theme['version_name']): ?>
                            <a class="btn btn-sm btn-warning" type="button"
                               href="update/<?= $theme['id'] ?>/<?= $localTheme->getVersion() ?>/<?= $localTheme->getName() ?>">
                                <?= LangManager::translate("core.Package.update") ?>
                            </a>
                        <?php endif; ?>
                        <form action="" method="post">
                            <?php (new SecurityManager())->insertHiddenToken() ?>
                            <input hidden type="text" name="theme" value="<?= $theme['name'] ?>">
                            <button type="submit" class="btn btn-sm btn-success">
                                <?= LangManager::translate("core.Theme.activate") ?>
                            </button>
                        </form>
                    </div>
                    <?php if ($localTheme->getVersion() !== $theme['version_name']): ?>
                        <div class="position-absolute"
                             style="transform: rotate(-45deg); left: -4em; top: 5em; margin: 0; z-index: 50">
                            <div class="alert-light-warning color-warning text-center "
                                 style="opacity: .85;padding-left: 4rem; padding-right: 4rem ">
                                <?= LangManager::translate("core.Theme.update") ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!--Details modal-->
            <div class="modal fade text-left w-100" id="modal-<?= $theme['id'] ?>" tabindex="-1"
                 role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl"
                     role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><?= $theme['name'] ?></h4>
                            <div class="d-flex justify-content-end mt-auto gap-3">
                                <a href="install/<?= $theme['id'] ?>" class="btn btn-sm btn-danger">
                                    <?= LangManager::translate("core.Theme.reinstall") ?>
                                </a>
                                <form action="" method="post">
                                    <?php (new SecurityManager())->insertHiddenToken() ?>
                                    <input hidden type="text" name="theme"
                                           value="<?= $theme['name'] ?>">
                                    <button type="submit"
                                            class="btn btn-sm btn-success"><?= LangManager::translate("core.Theme.activate") ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 col-lg-6 mb-3" style="height:20rem">
                                    <img style="height: 100%; width: 100%;"
                                         src="<?= $theme["icon"] ?>"
                                         alt="Icon <?= $theme['name'] ?>">
                                </div>
                                <div class="col-12 col-lg-6 position-relative">
                                    <p class="">
                                        <b><?= LangManager::translate("core.Theme.description") ?></b><br><?= $theme['description'] ?>
                                    </p>
                                    <hr>
                                    <p class="small">
                                        <?= LangManager::translate("core.Theme.author") ?>
                                        <i>
                                            <b>
                                                <a href="" target="_blank">
                                                    <?= $theme['author_pseudo'] ?>
                                                </a>
                                            </b>
                                        </i>
                                        <br>
                                        <?= LangManager::translate("core.Theme.downloads") ?>
                                        <i><b><?= $theme['downloads'] ?></b></i>
                                    </p>
                                    <p class="small">
                                        <?= LangManager::translate("core.Theme.themeVersion") ?>
                                        <i><b><?= $localTheme->getVersion() ?></b></i><br>
                                    </p>
                                    <?php if ($localTheme->getVersion() !== $theme['version_name']): ?>
                                        <p class="small">
                                            <?= LangManager::translate('core.Package.versionDistant') ?>
                                            :
                                            <i>
                                                <b><?= $theme['version_name'] ?></b>
                                            </i>
                                            <br>
                                        </p>
                                    <?php endif; ?>
                                    <?= LangManager::translate("core.Theme.CMWVersion") ?>
                                    <i><b><?= $theme['version_cmw'] ?></b></i>
                                    </p>
                                    <div class="d-flex gap-3 align-items-center">
                                        <?php if ($theme['demo']): ?>
                                            <a class="btn btn-sm btn-primary"
                                               href="<?= $theme['demo'] ?>" target="_blank"><i
                                                    class="fa-solid fa-arrow-up-right-from-square"></i> <?= LangManager::translate("core.Theme.demo") ?>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($theme['code_link']): ?>
                                            <a class="btn btn-sm btn-primary"
                                               href="<?= $theme['code_link'] ?>" target="_blank"><i
                                                    class="fa-brands fa-github"></i> GitHub</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary"
                                    data-bs-dismiss="modal"><?= LangManager::translate("core.Theme.close") ?></button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<!--------------------
-----MODAL de reset---
---------------------->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalTitle"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="confirmModalTitle"><?= LangManager::translate("core.Theme.verification") ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    <?= LangManager::translate("core.Theme.verificationText") ?>
                </p>
            </div>
            <div class="modal-footer">
                <form action="market/regenerate" method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <div class="button">
                        <button type="button" class="btn btn-light-primary" data-bs-dismiss="modal">
                            <?= LangManager::translate("core.btn.close") ?>
                        </button>
                        <button type="submit" class="btn btn-danger float-left">
                            <?= LangManager::translate("core.btn.confirm") ?>
                        </button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>