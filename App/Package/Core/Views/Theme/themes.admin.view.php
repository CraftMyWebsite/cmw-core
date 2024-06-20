<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Theme\ThemeManager;
use CMW\Utils\Website;

/* @var $currentTheme \CMW\Manager\Theme\IThemeConfig */
/* @var $installedThemes \CMW\Manager\Theme\IThemeConfig[] */
/* @var $themesList */

Website::setTitle(LangManager::translate("core.Theme.config.title"));
Website::setDescription(LangManager::translate("core.Theme.config.description")); ?>

<h3><i class="fa-solid fa-feather"></i> <?= LangManager::translate("core.Theme.myThemes") ?></h3>

<div class="grid-4 mb-24">
    <!------------------------------------
    -----Listage des thèmes local ACTIF---
    -------------------------------------->
    <?php foreach (ThemeManager::getInstance()->getLocalThemes() as $theme): ?>
        <?php if ($theme->name() === $currentTheme->name()): ?>
            <div class="card p-0 relative" style="overflow: hidden;">
                <div class="flex justify-between px-2 pt-2">
                    <p class="font-bold"><?= $theme->name() ?></p>
                    <button data-modal-toggle="modal-<?= $theme->name() ?>" class="btn-primary-sm" type="button"><?= LangManager::translate("core.Theme.details") ?></button>
                </div>
                <div class="relative">
                    <?php if ($theme->name() !== "Sampler"): ?>
                        <div class="bg-warning absolute bottom-0 w-full text-center "
                             style="opacity: .85; color: black">
                            <?= LangManager::translate("core.Theme.notVerified") ?>
                        </div>
                    <?php endif; ?>
                    <img style="height: 200px; width: 100%; object-fit: cover"
                         src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Img/local-theme.jpg"
                         alt="img">
                </div>
                <div class="text-center pb-2">
                    <a href="manage" type="button"
                       class="btn-primary-sm"><?= LangManager::translate("core.Theme.configure") ?></a>
                </div>
                <div class="absolute"
                     style="transform: rotate(-45deg); left: -3em; top: 3em; margin: 0; z-index: 10">
                    <div class="text-center"
                         style="opacity: .85; padding-left: 4.5rem; padding-right: 4.5rem; background-color: #3ab757; color: white">
                        <?= LangManager::translate("core.Theme.active") ?>
                    </div>
                </div>
            </div>

            <div id="modal-<?= $theme->name() ?>" class="modal-container">
                <div class="modal-lg">
                    <div class="modal-header">
                        <h6><?= $theme->name() ?></h6>
                        <div>
                            <a href="manage"
                               class="btn btn-sm btn-primary"><?= LangManager::translate("core.Theme.configure") ?></a>
                            <a href="market/regenerate"
                               class="btn btn-sm btn-warning"><?= LangManager::translate("core.Theme.reset") ?></a>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div style="height:20rem">
                            <img style="height: 100%; width: 100%;"
                                 src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Img/local-theme.jpg"
                                 alt="img">
                        </div>
                        <div>
                            <p class="">
                                <b><?= LangManager::translate("core.Theme.description") ?></b>
                            </p>
                            <?php if ($theme->name() !== "Sampler"): ?>
                                <p><?= LangManager::translate("core.Theme.descriptionManualInstall") ?></p>
                            <?php else: ?>
                                <p><?= LangManager::translate("core.Theme.descriptionIsSampler") ?></p>
                            <?php endif; ?>
                            <hr>
                            <p class="small">
                                <?= LangManager::translate("core.Theme.author") ?><a
                                    href=""
                                    target="_blank"><?= $theme->author() ?? $theme->authors() ?>
                                </a>
                            </p>
                            <p class="small">
                                <?= LangManager::translate("core.Theme.themeVersion") ?>
                                <i><b><?= $theme->version() ?></b></i><br>
                                <?= LangManager::translate("core.Theme.CMWVersion") ?>
                                <i><b><?= $theme->cmwVersion() ?></b></i>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-modal-hide="modal-<?= $theme->name() ?>" type="button" class="btn-danger">Fermer</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <!------------------------------------------------
    -----Listage des thèmes API installé et ACTIF---
    -------------------------------------------------->
    <?php foreach ($themesList as $theme): ?>
        <?php if ($theme['name'] === $currentTheme->name()): ?>
        <?php $localTheme = ThemeManager::getInstance()->getTheme($theme['name']); ?>
            <div class="card p-0 relative" style="overflow: hidden;">
                <div class="flex justify-between px-2 pt-2">
                    <p class="font-bold"><?= $theme['name'] ?></p>
                    <button data-modal-toggle="modal-<?= $theme['id'] ?>" class="btn-primary-sm" type="button"><?= LangManager::translate("core.Theme.details") ?></button>
                </div>
                <div class="relative">
                    <img style="height: 200px; width: 100%; object-fit: cover" src="<?= $theme["icon"] ?>"
                         alt="Icon <?= $theme['name'] ?>">
                </div>

                <div class="text-center pb-2">
                    <?php if ($localTheme->version() !== $theme['version_name']): ?>
                        <a class="btn-warning-sm" type="button"
                           href="update/<?= $theme['id'] ?>/<?= $localTheme->version() ?>/<?= $localTheme->name() ?>">
                            <?= LangManager::translate("core.Package.update") ?>
                        </a>
                    <?php endif; ?>
                    <a href="manage" type="button"
                       class="btn-primary-sm"><?= LangManager::translate("core.Theme.configure") ?></a>
                </div>
                <div class="absolute"
                     style="transform: rotate(-45deg); left: -3em; top: 3em; margin: 0; z-index: 10">
                    <div class="text-center"
                         style="opacity: .85; padding-left: 4.5rem; padding-right: 4.5rem; background-color: #3ab757; color: white">
                        <?= LangManager::translate("core.Theme.active") ?>
                    </div>
                </div>
                <?php if ($localTheme->version() !== $theme['version_name']): ?>
                    <div class="absolute"
                         style="transform: rotate(-45deg); left: -4em; top: 5em; margin: 0; z-index: 10">
                        <div class="text-center"
                             style="opacity: .85;padding-left: 4.5rem; padding-right: 4.5rem; background-color: rgb(245 158 11); color: white">
                            <?= LangManager::translate("core.Theme.update") ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div id="modal-<?= $theme['id'] ?>" class="modal-container">
                <div class="modal-lg">
                    <div class="modal-header">
                        <h6><?= $theme['name'] ?></h6>
                        <div>
                            <a href="manage"
                               class="btn-primary"><?= LangManager::translate("core.Theme.configure") ?></a>
                            <a href="market/regenerate"
                               class="btn-warning"><?= LangManager::translate("core.Theme.reset") ?></a>
                            <a href="install/<?= $theme['id'] ?>"
                               class="btn-danger"><?= LangManager::translate("core.Theme.reinstall") ?></a>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div style="height:20rem">
                            <img style="height: 100%; width: 100%;"
                                 src="<?= $theme["icon"] ?>"
                                 alt="img <?= $theme["name"] ?>">
                        </div>
                        <div>
                            <p class="">
                                <b><?= LangManager::translate("core.Theme.description") ?></b>
                            </p>
                            <p><?= $theme['description'] ?></p>
                            <hr>
                            <p class="small">
                                <?= LangManager::translate("core.Theme.author") ?><a
                                    href=""
                                    target="_blank"><?= $theme['author_pseudo'] ?>
                                </a>
                            </p>
                            <p>
                                <?= LangManager::translate("core.Theme.downloads") ?>
                                <i><b><?= $theme['downloads'] ?></b></i>
                            </p>
                            <p class="small">
                                <?= LangManager::translate("core.Theme.themeVersion") ?>
                                <i><b><?= $theme['version_name'] ?></b></i><br>
                                <?= LangManager::translate("core.Theme.CMWVersion") ?>
                                <i><b><?= $theme['version_cmw'] ?></b></i>
                            </p>
                        </div>
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
                    <div class="modal-footer">
                        <button data-modal-hide="modal-<?= $theme['id'] ?>" type="button" class="btn-danger">Fermer</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <!--------------------------------------
    -----Listage des thèmes locaux inactif---
    ---------------------------------------->
    <?php foreach (ThemeManager::getInstance()->getLocalThemes() as $theme): ?>
        <?php if ($theme->name() !== $currentTheme->name()): ?>
            <div class="card p-0 relative" style="overflow: hidden;">
                <div class="flex justify-between px-2 pt-2">
                    <p class="font-bold"><?= $theme->name() ?></p>
                    <button data-modal-toggle="modal-<?= $theme->name() ?>" class="btn-primary-sm" type="button"><?= LangManager::translate("core.Theme.details") ?></button>
                </div>
                <div class="relative">
                    <?php if ($theme->name() !== "Sampler"): ?>
                        <div class="bg-warning absolute bottom-0 w-full text-center "
                             style="opacity: .85; color: black">
                            <?= LangManager::translate("core.Theme.notVerified") ?>
                        </div>
                    <?php endif; ?>
                    <img class="rounded-bottom" style="height: 200px; width: 100%; object-fit: cover"
                         src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Img/local-theme.jpg"
                         alt="im">
                </div>
                <div class="text-center pb-2">
                    <form action="" method="post">
                        <?php (new SecurityManager())->insertHiddenToken() ?>
                        <input hidden type="text" name="theme" value="<?= $theme->name() ?>">
                        <button type="submit"
                                class="btn-success-sm"><?= LangManager::translate("core.Theme.activate") ?></button>
                    </form>
                </div>
            </div>
            <div id="modal-<?= $theme->name() ?>" class="modal-container">
                <div class="modal-lg">
                    <div class="modal-header">
                        <h6><?= $theme->name() ?></h6>
                        <div>
                            <form action="" method="post">
                                <?php (new SecurityManager())->insertHiddenToken() ?>
                                <input hidden type="text" name="theme"
                                       value="<?= $theme->name() ?>">
                                <button type="submit"
                                        class="btn btn-sm btn-success"><?= LangManager::translate("core.Theme.activate") ?></button>
                            </form>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div style="height:20rem">
                            <img style="height: 100%; width: 100%;"
                                 src="<?= EnvManager::getInstance()->getValue("PATH_SUBFOLDER") ?>Admin/Resources/Assets/Img/local-theme.jpg"
                                 alt="img">
                        </div>
                        <div>
                            <p class="">
                                <b><?= LangManager::translate("core.Theme.description") ?></b>
                            </p>
                            <?php if ($theme->name() !== "Sampler"): ?>
                                <p><?= LangManager::translate("core.Theme.descriptionManualInstall") ?></p>
                            <?php else: ?>
                                <p><?= LangManager::translate("core.Theme.descriptionIsSampler") ?></p>
                            <?php endif; ?>
                            <hr>
                            <p class="small">
                                <?= LangManager::translate("core.Theme.author") ?><a
                                    href=""
                                    target="_blank"><?= $theme->author() ?? $theme->authors() ?>
                                </a>
                            </p>
                            <p class="small">
                                <?= LangManager::translate("core.Theme.themeVersion") ?>
                                <i><b><?= $theme->version() ?></b></i><br>
                                <?= LangManager::translate("core.Theme.CMWVersion") ?>
                                <i><b><?= $theme->cmwVersion() ?></b></i>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-modal-hide="modal-<?= $theme->name() ?>" type="button" class="btn-danger">Fermer</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <!------------------------------------------------
    -----Listage des thèmes API installé et inactif---
    -------------------------------------------------->
    <?php foreach ($themesList as $theme): ?>
        <?php if ($theme['name'] !== $currentTheme->name() && ThemeManager::getInstance()->isThemeInstalled($theme['name'])): ?>
        <?php $localTheme = ThemeManager::getInstance()->getTheme($theme['name']); ?>
            <div class="card p-0 relative" style="overflow: hidden;">
                <div class="flex justify-between px-2 pt-2">
                    <p class="font-bold"><?= $theme['name'] ?></p>
                    <button data-modal-toggle="modal-<?= $theme['id'] ?>" class="btn-primary-sm" type="button"><?= LangManager::translate("core.Theme.details") ?></button>
                </div>
                <div class="relative">
                    <img style="height: 200px; width: 100%; object-fit: cover" src="<?= $theme["icon"] ?>"
                         alt="Icon <?= $theme['name'] ?>">
                </div>

                <div class="text-center pb-2">
                    <?php if ($localTheme->version() !== $theme['version_name']): ?>
                        <a class="btn-warning-sm" type="button"
                           href="update/<?= $theme['id'] ?>/<?= $localTheme->version() ?>/<?= $localTheme->name() ?>">
                            <?= LangManager::translate("core.Package.update") ?>
                        </a>
                    <?php endif; ?>
                    <a href="manage" type="button"
                       class="btn-primary-sm"><?= LangManager::translate("core.Theme.configure") ?></a>
                </div>
                <div class="absolute"
                     style="transform: rotate(-45deg); left: -3em; top: 3em; margin: 0; z-index: 10">
                    <div class="text-center"
                         style="opacity: .85; padding-left: 4.5rem; padding-right: 4.5rem; background-color: #3ab757; color: white">
                        <?= LangManager::translate("core.Theme.active") ?>
                    </div>
                </div>
                <?php if ($localTheme->version() !== $theme['version_name']): ?>
                    <div class="absolute"
                         style="transform: rotate(-45deg); left: -4em; top: 5em; margin: 0; z-index: 10">
                        <div class="text-center"
                             style="opacity: .85;padding-left: 4.5rem; padding-right: 4.5rem; background-color: rgb(245 158 11); color: white">
                            <?= LangManager::translate("core.Theme.update") ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div id="modal-<?= $theme['id'] ?>" class="modal-container">
                <div class="modal-lg">
                    <div class="modal-header">
                        <h6><?= $theme['name'] ?></h6>
                        <div>
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
                    <div class="grid-2">
                        <div style="height:20rem">
                            <img style="height: 100%; width: 100%;"
                                 src="<?= $theme["icon"] ?>"
                                 alt="img <?= $theme["name"] ?>">
                        </div>
                        <div>
                            <p class="">
                                <b><?= LangManager::translate("core.Theme.description") ?></b>
                            </p>
                            <p><?= $theme['description'] ?></p>
                            <hr>
                            <p class="small">
                                <?= LangManager::translate("core.Theme.author") ?><a
                                    href=""
                                    target="_blank"><?= $theme['author_pseudo'] ?>
                                </a>
                            </p>
                            <p>
                                <?= LangManager::translate("core.Theme.downloads") ?>
                                <i><b><?= $theme['downloads'] ?></b></i>
                            </p>
                            <p class="small">
                                <?= LangManager::translate("core.Theme.themeVersion") ?>
                                <i><b><?= $theme['version_name'] ?></b></i><br>
                                <?= LangManager::translate("core.Theme.CMWVersion") ?>
                                <i><b><?= $theme['version_cmw'] ?></b></i>
                            </p>
                        </div>
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
                    <div class="modal-footer">
                        <button data-modal-hide="modal-<?= $theme['id'] ?>" type="button" class="btn-danger">Fermer</button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>