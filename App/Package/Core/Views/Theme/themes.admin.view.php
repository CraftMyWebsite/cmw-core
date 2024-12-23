<?php

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Theme\IThemeConfig;
use CMW\Manager\Theme\ThemeManager;
use CMW\Utils\Website;

/* @var $currentTheme IThemeConfig */
/* @var $installedThemes IThemeConfig[] */
/* @var $themesList */

Website::setTitle(LangManager::translate('core.theme.config.title'));
Website::setDescription(LangManager::translate('core.theme.config.description'));
?>

<h3><i class="fa-solid fa-palette"></i> <?= LangManager::translate('core.theme.myThemes') ?></h3>

<div class="grid-4 mb-24">
    <!------------------------------------
    -----Listage des thèmes local ACTIF---
    -------------------------------------->
    <?php foreach (ThemeManager::getInstance()->getLocalThemes() as $theme): ?>
        <?php if ($theme->name() === $currentTheme->name()): ?>
            <div class="card p-0 relative" style="overflow: hidden;">
                <div class="flex justify-between px-2 pt-2">
                    <p class="font-bold"><?= $theme->name() ?></p>
                    <div>
                        <button data-modal-toggle="modal-<?= $theme->name() ?>" class="btn-primary-sm"
                                type="button"><?= LangManager::translate('core.theme.details') ?></button>
                        <?php if ($theme->name() !== 'Sampler'): ?>
                            <button data-modal-toggle="modal-delete-<?= $theme->name() ?>" class="btn-danger-sm"
                                    type="button"><i class="fa-solid fa-trash"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="relative">
                    <?php if ($theme->name() !== 'Sampler'): ?>
                        <div class="bg-warning absolute bottom-0 w-full text-center "
                             style="opacity: .85; color: black">
                            <?= LangManager::translate('core.theme.notVerified') ?>
                        </div>
                    <?php endif; ?>
                    <img style="height: 200px; width: 100%; object-fit: cover"
                         src="<?= $theme->imageLink() ?? ThemeManager::getInstance()->defaultImageLink() ?>"
                         loading="lazy"
                         alt="img">
                </div>
                <div class="text-center pb-2">
                    <a href="manage" type="button"
                       class="btn-primary-sm"><?= LangManager::translate('core.theme.configure') ?></a>
                </div>
                <div class="absolute"
                     style="transform: rotate(-45deg); left: -3em; top: 3em; margin: 0; z-index: 10">
                    <div class="text-center"
                         style="opacity: .85; padding-left: 4.5rem; padding-right: 4.5rem; background-color: #3ab757; color: white">
                        <?= LangManager::translate('core.theme.active') ?>
                    </div>
                </div>
            </div>

            <div id="modal-<?= $theme->name() ?>" class="modal-container">
                <div class="modal-xl">
                    <div class="modal-header">
                        <h6><?= $theme->name() ?></h6>
                        <div>
                            <a href="manage"
                               class="btn btn-sm btn-primary"><?= LangManager::translate('core.theme.configure') ?></a>
                            <a href="market/regenerate"
                               class="btn btn-sm btn-warning"><?= LangManager::translate('core.theme.reset') ?></a>
                        </div>
                    </div>
                    <div class="modal-body grid-2">
                        <div style="height:20rem">
                            <img style="height: 100%; width: 100%;"
                                 src="<?= $theme->imageLink() ?? ThemeManager::getInstance()->defaultImageLink() ?>"
                                 loading="lazy"
                                 alt="img">
                        </div>
                        <div>
                            <p class="">
                                <b><?= LangManager::translate('core.theme.description') ?></b>
                            </p>
                            <?php if ($theme->name() !== 'Sampler'): ?>
                                <p><?= LangManager::translate('core.theme.descriptionManualInstall') ?></p>
                            <?php else: ?>
                                <p><?= LangManager::translate('core.theme.descriptionIsSampler') ?></p>
                            <?php endif; ?>
                            <hr>
                            <p class="small">
                                <?= LangManager::translate('core.theme.author') ?><a
                                    href=""
                                    target="_blank"><?= $theme->author() ?? $theme->authors() ?>
                                </a>
                            </p>
                            <p class="small">
                                <?= LangManager::translate('core.theme.themeVersion') ?>
                                <i><b><?= $theme->version() ?></b></i><br>
                                <?= LangManager::translate('core.theme.CMWVersion') ?>
                                <i><b><?= $theme->cmwVersion() ?></b></i>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-modal-hide="modal-<?= $theme->name() ?>" type="button" class="btn-danger">
                            <?= LangManager::translate('core.btn.close') ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php if ($theme->name() !== 'Sampler'): ?>
                <div id="modal-delete-<?= $theme->name() ?>" class="modal-container">
                    <div class="modal">
                        <div class="modal-header-danger">
                            <h6><?= LangManager::translate('core.theme.toasters.delete.title') ?> <?= $theme->name() ?> ?</h6>
                        </div>
                        <div class="modal-body">
                            <p><?= LangManager::translate('core.theme.toasters.delete.confirm') ?> <?= $theme->name() ?> ?</p>
                            <p><?= LangManager::translate('core.theme.toasters.delete.config') ?></p>
                        </div>
                        <div class="modal-footer">
                            <a href="theme/delete/<?= base64_encode($theme->name()) ?>" type="button" class="btn-danger"><?= LangManager::translate('core.theme.toasters.delete.title') ?></a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
                    <div>
                        <button data-modal-toggle="modal-<?= $theme['id'] ?>" class="btn-primary-sm"
                                type="button"><?= LangManager::translate('core.theme.details') ?></button>
                        <button data-modal-toggle="modal-delete-<?= $theme['id'] ?>" class="btn-danger-sm"
                                type="button"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
                <div class="relative">
                    <img style="height: 200px; width: 100%; object-fit: cover" src="<?= $theme['icon'] ?>"
                         loading="lazy"
                         alt="Icon <?= $theme['name'] ?>">
                </div>

                <div class="text-center pb-2">
                    <?php if ($theme['version_status'] === 0 && $localTheme->version() !== $theme['version_name']): ?>
                        <a class="btn-warning-sm" type="button"
                           href="update/<?= $theme['id'] ?>/<?= $localTheme->version() ?>/<?= $localTheme->name() ?>">
                            <?= LangManager::translate('core.Package.update') ?>
                        </a>
                    <?php endif; ?>
                    <a href="manage" type="button"
                       class="btn-primary-sm"><?= LangManager::translate('core.theme.configure') ?></a>
                </div>
                <div class="absolute"
                     style="transform: rotate(-45deg); left: -3em; top: 3em; margin: 0; z-index: 10">
                    <div class="text-center"
                         style="opacity: .85; padding-left: 4.5rem; padding-right: 4.5rem; background-color: #3ab757; color: white">
                        <?= LangManager::translate('core.theme.active') ?>
                    </div>
                </div>
                <?php if ($theme['version_status'] === 0 && $localTheme->version() !== $theme['version_name']): ?>
                    <div class="absolute"
                         style="transform: rotate(-45deg); left: -4em; top: 5em; margin: 0; z-index: 10">
                        <div class="text-center"
                             style="opacity: .85;padding-left: 4.5rem; padding-right: 4.5rem; background-color: rgb(245 158 11); color: white">
                            <?= LangManager::translate('core.theme.update') ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div id="modal-<?= $theme['id'] ?>" class="modal-container">
                <div class="modal-xl">
                    <div class="modal-header">
                        <h6><?= $theme['name'] ?></h6>
                        <div>
                            <a href="manage"
                               class="btn-primary"><?= LangManager::translate('core.theme.configure') ?></a>
                            <a href="market/regenerate"
                               class="btn-warning"><?= LangManager::translate('core.theme.reset') ?></a>
                            <a href="install/<?= $theme['id'] ?>"
                               class="btn-danger"><?= LangManager::translate('core.theme.reinstall') ?></a>
                        </div>
                    </div>
                    <div class="modal-body">
                        <?php if ($theme['version_status'] === 0 && $localTheme->version() !== $theme['version_name']): ?>
                            <div class="alert-warning">
                                <?= LangManager::translate('core.theme.manage.theme_need_update',
                                    ['version' => $localTheme->version(), 'target' => $theme['version_name']]) ?>
                            </div>
                        <?php endif; ?>
                        <div class="grid-2">
                            <div style="height:20rem">
                                <img style="height: 100%; width: 100%;"
                                     src="<?= $theme['icon'] ?>"
                                     loading="lazy"
                                     alt="img <?= $theme['name'] ?>">
                            </div>
                            <div>
                                <p class="">
                                    <b><?= LangManager::translate('core.theme.description') ?></b>
                                </p>
                                <p><?= htmlspecialchars_decode($theme['description']) ?></p>
                                <hr>
                                <p class="small">
                                    <?= LangManager::translate('core.theme.author') ?><a
                                        href="https://craftmywebsite.fr/market/user/<?= $theme['author_pseudo'] ?>"
                                        target="_blank" class="link"><?= $theme['author_pseudo'] ?>
                                    </a>
                                </p>
                                <p>
                                    <?= LangManager::translate('core.theme.downloads') ?>
                                    <i><b><?= $theme['downloads'] ?></b></i>
                                </p>
                                <p class="small">
                                    <?= LangManager::translate('core.theme.localThemeVersion') ?>
                                    <i><b><?= $localTheme->version() ?></b></i><br>
                                    <?= LangManager::translate('core.theme.themeVersion') ?>
                                    <i><b><?= $theme['version_name'] ?></b>
                                        <?php if ($theme['version_status'] !== 0): ?>
                                            <small class="text-warning">En cours de vérification</small>
                                        <?php endif; ?></i>
                                    <br>
                                    <?= LangManager::translate('core.theme.CMWVersion') ?>
                                    <i><b><?= $theme['version_cmw'] ?></b></i>
                                </p>
                                <div class="flex gap-3">
                                    <?php if (isset($theme['demo'])): ?>
                                        <a class="btn-primary-sm"
                                           href="<?= $theme['demo'] ?>" target="_blank"><i
                                                class="fa-solid fa-arrow-up-right-from-square"></i> <?= LangManager::translate('core.theme.demo') ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($theme['code_link']): ?>
                                        <a class="btn-primary-sm"
                                           href="<?= $theme['code_link'] ?>" target="_blank">
                                            <i class="fa-brands fa-git"></i>
                                            <?= LangManager::translate('core.source_code') ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-modal-hide="modal-<?= $theme['id'] ?>" type="button" class="btn-danger">
                            <?= LangManager::translate('core.btn.close') ?>
                        </button>
                    </div>
                </div>
            </div>
            <div id="modal-delete-<?= $theme['id'] ?>" class="modal-container">
                <div class="modal">
                    <div class="modal-header-danger">
                        <h6><?= LangManager::translate('core.theme.toasters.delete.title') ?> <?= $theme['name'] ?> ?</h6>
                    </div>
                    <div class="modal-body">
                        <p><?= LangManager::translate('core.theme.toasters.delete.confirm') ?> <?= $theme['name'] ?> ?</p>
                        <p><?= LangManager::translate('core.theme.toasters.delete.config') ?></p>
                    </div>
                    <div class="modal-footer">
                        <a href="theme/delete/<?= base64_encode($theme['name']) ?>" type="button" class="btn-danger"><?= LangManager::translate('core.theme.toasters.delete.title') ?></a>
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
                    <div>
                        <button data-modal-toggle="modal-<?= $theme->name() ?>" class="btn-primary-sm"
                                type="button"><?= LangManager::translate('core.theme.details') ?></button>
                        <?php if ($theme->name() !== 'Sampler'): ?>
                            <button data-modal-toggle="modal-delete-<?= $theme->name() ?>" class="btn-danger-sm"
                                    type="button"><i class="fa-solid fa-trash"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="relative">
                    <?php if ($theme->name() !== 'Sampler'): ?>
                        <div class="bg-warning absolute bottom-0 w-full text-center "
                             style="opacity: .85; color: black">
                            <?= LangManager::translate('core.theme.notVerified') ?>
                        </div>
                    <?php endif; ?>
                    <img class="rounded-bottom" style="height: 200px; width: 100%; object-fit: cover"
                         src="<?= $theme->imageLink() ?? ThemeManager::getInstance()->defaultImageLink() ?>"
                         loading="lazy"
                         alt="im">
                </div>
                <div class="text-center pb-2">
                    <form action="" method="post">
                        <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                        <input hidden type="text" name="theme" value="<?= $theme->name() ?>">
                        <button type="submit"
                                class="btn-success-sm"><?= LangManager::translate('core.theme.activate') ?></button>
                    </form>
                </div>
            </div>
            <div id="modal-<?= $theme->name() ?>" class="modal-container">
                <div class="modal-xl">
                    <div class="modal-header">
                        <h6><?= $theme->name() ?></h6>
                        <div>
                            <form action="" method="post">
                                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                <input hidden type="text" name="theme"
                                       value="<?= $theme->name() ?>">
                                <button type="submit"
                                        class="btn btn-sm btn-success"><?= LangManager::translate('core.theme.activate') ?></button>
                            </form>
                        </div>
                    </div>
                    <div class="grid-2 modal-body">
                        <div style="height:20rem">
                            <img style="height: 100%; width: 100%;"
                                 src="<?= $theme->imageLink() ?? ThemeManager::getInstance()->defaultImageLink() ?>"
                                 loading="lazy"
                                 alt="img">
                        </div>
                        <div>
                            <p class="">
                                <b><?= LangManager::translate('core.theme.description') ?></b>
                            </p>
                            <?php if ($theme->name() !== 'Sampler'): ?>
                                <p><?= LangManager::translate('core.theme.descriptionManualInstall') ?></p>
                            <?php else: ?>
                                <p><?= LangManager::translate('core.theme.descriptionIsSampler') ?></p>
                            <?php endif; ?>
                            <hr>
                            <p class="small">
                                <?= LangManager::translate('core.theme.author') ?><a
                                    href=""
                                    target="_blank"><?= $theme->author() ?? $theme->authors() ?>
                                </a>
                            </p>
                            <p class="small">
                                <?= LangManager::translate('core.theme.themeVersion') ?>
                                <i><b><?= $theme->version() ?></b></i><br>
                                <?= LangManager::translate('core.theme.CMWVersion') ?>
                                <i><b><?= $theme->cmwVersion() ?></b></i>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-modal-hide="modal-<?= $theme->name() ?>" type="button" class="btn-danger">
                            <?= LangManager::translate('core.btn.close') ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php if ($theme->name() !== 'Sampler'): ?>
                <div id="modal-delete-<?= $theme->name() ?>" class="modal-container">
                    <div class="modal">
                        <div class="modal-header-danger">
                            <h6><?= LangManager::translate('core.theme.toasters.delete.title') ?> <?= $theme->name() ?> ?</h6>
                        </div>
                        <div class="modal-body">
                            <p><?= LangManager::translate('core.theme.toasters.delete.confirm') ?> <?= $theme->name() ?> ?</p>
                            <p><?= LangManager::translate('core.theme.toasters.delete.config') ?></p>
                        </div>
                        <div class="modal-footer">
                            <a href="theme/delete/<?= base64_encode($theme->name()) ?>" type="button" class="btn-danger"><?= LangManager::translate('core.theme.toasters.delete.title') ?></a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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
                    <div>
                        <button data-modal-toggle="modal-<?= $theme['id'] ?>" class="btn-primary-sm"
                                type="button"><?= LangManager::translate('core.theme.details') ?></button>
                        <button data-modal-toggle="modal-delete-<?= $theme['id'] ?>" class="btn-danger-sm"
                                type="button"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
                <div class="relative">
                    <img style="height: 200px; width: 100%; object-fit: cover" src="<?= $theme['icon'] ?>"
                         loading="lazy"
                         alt="Icon <?= $theme['name'] ?>">
                </div>

                <div class="text-center pb-2">
                    <?php if ($theme['version_status'] === 0 && $localTheme->version() !== $theme['version_name']): ?>
                        <a class="btn-warning-sm" type="button"
                           href="update/<?= $theme['id'] ?>/<?= $localTheme->version() ?>/<?= $localTheme->name() ?>">
                            <?= LangManager::translate('core.Package.update') ?>
                        </a>
                    <?php else: ?>
                        <form action="" method="post">
                            <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                            <input hidden type="text" name="theme"
                                   value="<?= $theme['name'] ?>">
                            <button type="submit"
                                    class="btn-success-sm"><?= LangManager::translate('core.theme.activate') ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
                <?php if ($theme['version_status'] === 0 && $localTheme->version() !== $theme['version_name']): ?>
                    <div class="absolute"
                         style="transform: rotate(-45deg); left: -4em; top: 5em; margin: 0; z-index: 10">
                        <div class="text-center"
                             style="opacity: .85;padding-left: 4.5rem; padding-right: 4.5rem; background-color: rgb(245 158 11); color: white">
                            <?= LangManager::translate('core.theme.update') ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div id="modal-<?= $theme['id'] ?>" class="modal-container">
                <div class="modal-xl">
                    <div class="modal-header">
                        <h6><?= $theme['name'] ?></h6>
                        <div>
                            <form action="" method="post">
                                <?php SecurityManager::getInstance()->insertHiddenToken() ?>
                                <input hidden type="text" name="theme"
                                       value="<?= $theme['name'] ?>">
                                <button type="submit"
                                        class="btn btn-sm btn-success"><?= LangManager::translate('core.theme.activate') ?>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="modal-body">
                        <?php if ($theme['version_status'] === 0 && $localTheme->version() !== $theme['version_name']): ?>
                            <div class="alert-warning">
                                <?= LangManager::translate('core.theme.manage.theme_need_update',
                                    ['version' => $localTheme->version(), 'target' => $theme['version_name']]) ?>
                            </div>
                        <?php endif; ?>
                        <div class="grid-2">
                            <div style="height:20rem">
                                <img style="height: 100%; width: 100%;"
                                     src="<?= $theme['icon'] ?>"
                                     loading="lazy"
                                     alt="img <?= $theme['name'] ?>">
                            </div>
                            <div>
                                <p class="">
                                    <b><?= LangManager::translate('core.theme.description') ?></b>
                                </p>
                                <?= htmlspecialchars_decode($theme['description']) ?>
                                <hr>
                                <p class="small">
                                    <?= LangManager::translate('core.theme.author') ?><a
                                        href="https://craftmywebsite.fr/market/user/<?= $theme['author_pseudo'] ?>"
                                        target="_blank" class="link"><?= $theme['author_pseudo'] ?>
                                    </a>
                                </p>
                                <p>
                                    <?= LangManager::translate('core.theme.downloads') ?>
                                    <i><b><?= $theme['downloads'] ?></b></i>
                                </p>
                                <p class="small">
                                    <?= LangManager::translate('core.theme.localThemeVersion') ?>
                                    <i><b><?= $localTheme->version() ?></b></i><br>
                                    <?= LangManager::translate('core.theme.themeVersion') ?>
                                    <i><b><?= $theme['version_name'] ?></b>
                                        <?php if ($theme['version_status'] !== 0): ?>
                                            <small class="text-warning">En cours de vérification</small>
                                        <?php endif; ?></i>
                                    <br>
                                    <?= LangManager::translate('core.theme.CMWVersion') ?>
                                    <i><b><?= $theme['version_cmw'] ?></b></i>
                                </p>
                                <div class="flex gap-3">
                                    <?php if (isset($theme['demo'])): ?>
                                        <a class="btn-primary-sm"
                                           href="<?= $theme['demo'] ?>" target="_blank"><i
                                                class="fa-solid fa-arrow-up-right-from-square"></i> <?= LangManager::translate('core.theme.demo') ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($theme['code_link']): ?>
                                        <a class="btn-primary-sm"
                                           href="<?= $theme['code_link'] ?>" target="_blank">
                                            <i class="fa-brands fa-git"></i>
                                            <?= LangManager::translate('core.source_code') ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-modal-hide="modal-<?= $theme['id'] ?>" type="button" class="btn-danger">
                            <?= LangManager::translate('core.btn.close') ?>
                        </button>
                    </div>
                </div>
            </div>
            <div id="modal-delete-<?= $theme['id'] ?>" class="modal-container">
                <div class="modal">
                    <div class="modal-header-danger">
                        <h6><?= LangManager::translate('core.theme.toasters.delete.title') ?> <?= $theme['name'] ?> ?</h6>
                    </div>
                    <div class="modal-body">
                        <p><?= LangManager::translate('core.theme.toasters.delete.confirm') ?> <?= $theme['name'] ?> ?</p>
                        <p><?= LangManager::translate('core.theme.toasters.delete.config') ?></p>
                    </div>
                    <div class="modal-footer">
                        <a href="theme/delete/<?= base64_encode($theme['name']) ?>" type="button" class="btn-danger"><?= LangManager::translate('core.theme.toasters.delete.title') ?></a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>