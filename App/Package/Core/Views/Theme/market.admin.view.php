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
    <h3><i class="fa-solid fa-feather"></i> <span
            class="m-lg-auto"><?= LangManager::translate("core.Theme.market") ?></span></h3>
</div>

<section class="row">
    <!----------------------------------------
    -----Listage des thèmes API non nstallé---
    ------------------------------------------>
    <?php foreach ($themesList as $theme): ?>
        <?php if (!ThemeController::isThemeInstalled($theme['name'])): ?>
            <div class="col-12 col-lg-3 mb-4">
                <div class="card">
                    <div class="d-flex justify-content-between align-items-center px-2 py-2">
                        <b><?= $theme['name'] ?></b>
                        <button type="button" data-bs-toggle="modal"
                                data-bs-target="#modal-<?= $theme['id'] ?>"
                                class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.details") ?></button>
                    </div>
                    <div class="position-relative">
                        <img style="height: 200px; width: 100%; object-fit: cover" src="<?= $theme["icon"] ?>"
                             alt="Icon <?= $theme['name'] ?>">
                    </div>
                    <div class="d-flex justify-content-center px-2 py-1">
                        <button onclick="this.disabled = true; window.location = 'install/<?= $theme['id'] ?>'" class="btn btn-sm btn-primary"><i
                                class="fa-solid fa-download"></i> <?= LangManager::translate("core.Theme.install") ?>
                        </button>
                    </div>
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
                                <button onclick="this.disabled = true; window.location = 'install/<?= $theme['id'] ?>'"
                                   class="btn btn-sm btn-primary"><i
                                        class="fa-solid fa-download"></i> <?= LangManager::translate("core.Theme.install") ?>
                                </button>
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
</section>