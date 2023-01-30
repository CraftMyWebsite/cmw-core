<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;

/* @var $currentTheme \CMW\Entity\Core\ThemeEntity */
/* @var $installedThemes \CMW\Entity\Core\ThemeEntity[] */
/* @var $themesList */

$title = LangManager::translate("core.theme.config.title");
$description = LangManager::translate("core.theme.config.description"); ?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-sliders"></i> <span class="m-lg-auto">Configuration</span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-3">
        <div class="card">
            <div class="card-header">
                <h4>Thèmes</h4>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <?php (new SecurityManager())->insertHiddenToken() ?>
                    <h6>Actif :</h6>
                    <fieldset class="form-group">
                        <select class="form-select" id="basicSelect" name="theme">
                            <?php foreach ($installedThemes as $theme): ?>
                                <option value="<?= $theme->getName() ?>" <?= $theme->getName() === $currentTheme->getName() ? "selected" : "" ?>>
                                    <?= $theme->getName() ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </fieldset>
                    <div class="d-flex flex-wrap justify-content-between mt-4">
                        <button type="button" data-bs-toggle="modal" data-bs-target="#confirmModal"
                                class="btn btn-warning">Réinitialiser
                        </button>
                        <button type="submit"
                                class="btn btn-primary"><?= LangManager::translate("core.btn.save") ?></button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-9">
        <div class="card">
            <div class="card-header">
                <h4>Installation rapide</h4>
            </div>
            <div class="card-body">
                <table class="table" id="table1">
                    <thead>
                    <tr>
                        <th class="text-center"><?= LangManager::translate("core.theme.config.list.name") ?></th>
                        <th class="text-center"><?= LangManager::translate("core.theme.config.list.version") ?></th>
                        <th class="text-center"><?= LangManager::translate("core.theme.config.list.cmw_version") ?></th>
                        <th class="text-center"><?= LangManager::translate("core.theme.config.list.downloads") ?></th>
                        <th class="text-center"><?= LangManager::translate("core.theme.config.list.download") ?></th>
                    </tr>
                    </thead>
                    <tbody class="text-center">
                    <?php foreach ($themesList as $theme): ?>
                        <tr>
                            <td class="text-center"><?= $theme->title ?></td>
                            <td class="text-center"><?= $theme->version ?></td>
                            <td class="text-center"><?= $theme->version_cmw ?></td>
                            <td class="text-center"><?= $theme->downloads ?></td>
                            <td class="text-center"><a href="install/<?= $theme->id ?>"
                                                       class="btn btn-sm btn-primary"><i
                                            class="fa-solid fa-download"></i> Installer</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>


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
                <form action="configuration/regenerate" method="post">
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
