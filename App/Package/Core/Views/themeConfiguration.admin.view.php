<?php

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Security\SecurityManager;
use CMW\Manager\Api\PublicAPI;

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
                    <a class="nav-link active" id="setting1-tab" data-bs-toggle="tab" href="#setting1" role="tab" aria-selected="true">Mes thèmes</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="setting2-tab" data-bs-toggle="tab" href="#setting2" role="tab" aria-selected="false">Parcourir les thèmes</a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active py-2" id="setting1" role="tabpanel" aria-labelledby="setting1-tab">
                    <div class="row">
                        <?php foreach ($themesList as $theme): ?>
                            <?php if ($theme['name'] === $currentTheme->getName()):?>
                            <div class="col-12 col-lg-3">
                                <div class="card-in-card">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-2">
                                                <h4 class="card-title"><?= $theme['name'] ?></h4>
                                                <a href="<?= $theme['demo'] ?>" target="_blank">Démo</a>
                                            </div>
                                            <img style="height: 170px; width: 100%; margin-bottom: 4px;" src="<?= PublicAPI::getUrl() . '/' . $theme["icon"] ?>" alt="Card image cap">
                                            <p class=""><?= $theme['description'] ?></p>
                                            <?php foreach ($theme['tags'] as $tag): ?>
                                                <small class="px-1 bg-primary rounded mr-2"><?= $tag['value'] ?></small>
                                            <?php endforeach; ?>
                                            <p>
                                                Version CMW recommandée : <?= $theme['version_cmw'] ?><br>
                                                Version du thème : <?= $theme['version_name'] ?>  
                                            </p>
                                            <div class="d-flex justify-content-between">
                                                <p><a href="" target="_blank"><i class='fa-solid fa-at'></i><i> <?= $theme['author_pseudo'] ?></i></a></p>
                                                <p><a href="<?= $theme['code_link'] ?>" target="_blank"><i class="fa-brands fa-github"></i></a></p>
                                                <p><i class="fa-solid fa-download"></i> <?= $theme['downloads'] ?></p>
                                            </div>
                                            <div class="d-flex justify-content-around">
                                                <a href="manage" class="btn btn-sm btn-primary">Configurer</a>
                                                <button type="button" data-bs-toggle="modal" data-bs-target="#confirmModal"class="btn btn-sm btn-warning">Réinitialiser</button>
                                                <a href="install/<?= $theme['id'] ?>" class="btn btn-sm btn-danger">Réinstaller</a>
                                            </div>
                                        </div>
                                        <div class="bg-info text-white text-center rounded-3 rounded-top py-1"><b>Thème actif</b></div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                            <?php if ($currentTheme->getName() === "Sampler") : ?>
                            <div class="col-12 col-lg-3">
                                <div class="card-in-card">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <h4 class="card-title">Sampler</h4>
                                            <img style="height: 170px; width: 100%; margin-bottom: 4px;" src="https://www.freecodecamp.org/news/content/images/2020/01/html-examples.jpeg" alt="im">
                                            <p class="">Ce thème est celui de base</p>
                                            <p>
                                                Version CMW recommandée : ALL<br>
                                                Version du thème : 1.0  
                                            </p>
                                            <p><a href="" target="_blank"><i class='fa-solid fa-at'></i><i> CraftMyWebsite</i></a></p>
                                            <div class="d-flex justify-content-around">
                                                <a href="manage" class="btn btn-sm btn-primary">Configurer</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-info text-white text-center rounded-3 rounded-top py-1"><b>Thème actif</b></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php foreach ($themesList as $theme): ?>
                        <?php if (ThemeController::isThemeInstalled($theme['name']) && $theme['name'] != $currentTheme->getName()):?>
                            <div class="col-12 col-lg-3">
                            <div class="card-in-card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <h4 class="card-title"><?= $theme['name'] ?></h4>
                                            <a href="<?= $theme['demo'] ?>" target="_blank">Démo</a>
                                        </div>
                                        <img style="height: 170px; width: 100%; margin-bottom: 4px;" src="<?= PublicAPI::getUrl() . '/' . $theme["icon"] ?>" alt="Card image cap">
                                        <p class=""><?= $theme['description'] ?></p>
                                        <?php foreach ($theme['tags'] as $tag): ?>
                                            <small class="px-1 bg-primary rounded mr-2"><?= $tag['value'] ?></small>
                                        <?php endforeach; ?>
                                        <p>
                                            Version CMW recommandée : <?= $theme['version_cmw'] ?><br>
                                            Version du thème : <?= $theme['version_name'] ?>  
                                        </p>
                                        <div class="d-flex justify-content-between">
                                            <p><a href="" target="_blank"><i class='fa-solid fa-at'></i><i> <?= $theme['author_pseudo'] ?></i></a></p>
                                            <p><a href="<?= $theme['code_link'] ?>" target="_blank"><i class="fa-brands fa-github"></i></a></p>
                                            <p><i class="fa-solid fa-download"></i> <?= $theme['downloads'] ?></p>
                                        </div>
                                        <div class="d-flex justify-content-around">
                                            <a href="install/<?= $theme['id'] ?>" class="btn btn-sm btn-danger">Réinstaller</a>
                                            <form action="" method="post">
                                                <?php (new SecurityManager())->insertHiddenToken() ?>
                                                <input hidden type="text" name="theme" value="<?= $theme['name'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Activer</a>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($currentTheme->getName() != "Sampler"): ?>
                            <div class="col-12 col-lg-3">
                                <div class="card-in-card">
                                    <div class="card-content">
                                        <div class="card-body">
                                            <h4 class="card-title">Sampler</h4>
                                            <img style="height: 170px; width: 100%; margin-bottom: 4px;" src="https://www.freecodecamp.org/news/content/images/2020/01/html-examples.jpeg" alt="im">
                                            <p class="">Ce thème est celui de base</p>
                                            <p>
                                                Version CMW recommandée : ALL<br>
                                                Version du thème : 1.0  
                                            </p>
                                            <p><a href="" target="_blank"><i class='fa-solid fa-at'></i><i> CraftMyWebsite</i></a></p>
                                            <div class="d-flex justify-content-around">
                                                <form action="" method="post">
                                                <?php (new SecurityManager())->insertHiddenToken() ?>
                                                <input hidden type="text" name="theme" value="Sampler">
                                                <button type="submit" class="btn btn-sm btn-success">Activer</a>
                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                    </div>
                </div>
                <div class="tab-pane fade py-2" id="setting2" role="tabpanel" aria-labelledby="setting2-tab">
                    <div class="row">
    <?php foreach ($themesList as $theme): ?>

        <?php if ($theme['name'] != ThemeController::isThemeInstalled($theme['name'])):?>
            <div class="col-12 col-lg-3">
        <div class="card-in-card">
            <div class="card-content">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h4 class="card-title"><?= $theme['name'] ?></h4>
                        <a href="<?= $theme['demo'] ?>" target="_blank">Démo</a>
                    </div>
                    <img style="height: 170px; width: 100%; margin-bottom: 4px;" src="<?= PublicAPI::getUrl() . '/' . $theme["icon"] ?>" alt="Card image cap">
                    <p class=""><?= $theme['description'] ?></p>
                    <?php foreach ($theme['tags'] as $tag): ?>
                        <small class="px-1 bg-primary rounded mr-2"><?= $tag['value'] ?></small>
                    <?php endforeach; ?>
                    <p>
                        Version CMW recommandée : <?= $theme['version_cmw'] ?><br>
                        Version du thème : <?= $theme['version_name'] ?>  
                    </p>
                    <div class="d-flex justify-content-between">
                        <p><a href="" target="_blank"><i class='fa-solid fa-at'></i><i> <?= $theme['author_pseudo'] ?></i></a></p>
                        <p><a href="<?= $theme['code_link'] ?>" target="_blank"><i class="fa-brands fa-github"></i></a></p>
                        <p><i class="fa-solid fa-download"></i> <?= $theme['downloads'] ?></p>
                    </div>
                    <div class="d-flex justify-content-around">
                        <a href="install/<?= $theme['id'] ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-download"></i> Installer</a>
                    </div>
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