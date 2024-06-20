<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Theme\ThemeManager;
use CMW\Utils\Website;

/* @var $themesList */

Website::setTitle(LangManager::translate("core.Theme.config.title"));
Website::setDescription(LangManager::translate("core.Theme.config.description")); ?>

<h3><i class="fa-solid fa-palette"></i> <?= LangManager::translate("core.Theme.market") ?></h3>

<div class="grid-4">
    <?php foreach ($themesList as $theme): ?>
        <?php if (!ThemeManager::getInstance()->isThemeInstalled($theme['name'])): ?>
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
                    <button onclick="this.disabled = true; window.location = 'install/<?= $theme['id'] ?>'"
                            class="btn btn-sm btn-primary-sm"><i
                            class="fa-solid fa-download"></i> <?= LangManager::translate("core.Theme.install") ?>
                    </button>
                </div>
            </div>
            <div id="modal-<?= $theme['id'] ?>" class="modal-container">
                <div class="modal-lg">
                    <div class="modal-header">
                        <h6><?= $theme['name'] ?></h6>
                        <div>
                            <button onclick="this.disabled = true; window.location = 'install/<?= $theme['id'] ?>'"
                                    class="btn-primary"><i
                                    class="fa-solid fa-download"></i> <?= LangManager::translate("core.Theme.install") ?>
                            </button>
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
                            <div class="flex gap-3">
                                <?php if ($theme['demo']): ?>
                                    <a class="btn-primary-sm"
                                       href="<?= $theme['demo'] ?>" target="_blank"><i
                                            class="fa-solid fa-arrow-up-right-from-square"></i> <?= LangManager::translate("core.Theme.demo") ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($theme['code_link']): ?>
                                    <a class="btn-primary-sm"
                                       href="<?= $theme['code_link'] ?>" target="_blank"><i
                                            class="fa-brands fa-github"></i> GitHub</a>
                                <?php endif; ?>
                            </div>
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