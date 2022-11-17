<?php

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;

$title = LangManager::translate("core.updates.title");
$description = LangManager::translate("core.updates.description"); ?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fas fa-arrows-rotate"></i> <span class="m-lg-auto">Mises à jours</span></h3>
</div>

<section class="row">
    <div class="col-12 col-lg-3">
        <div class="card">
            <div class="card-header">
                <h4>CraftMyWebsite</h4>
            </div>
            <div class="card-body">
                <p>Version installé : 
                    <?php if (Utils::getVersion() != Utils::getLatestVersion()) {echo "<b class='text-danger'>". Utils::getVersion() . "</b>";} else {echo "<b class='text-sucess'>". Utils::getVersion() . "</b>";}
                    ?>
                </p>
                <p>Dernière version : <b><?= Utils::getLatestVersion() ?></b></p>
                <?php if (Utils::isNewUpdateAvailable()): ?>
                <div class="buttons text-center">
                    <a href="cms/install" type="button" class="btn btn-primary">Mettre à jours</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-9">
        <div class="card">
            <div class="card-header">
                <h4>Changelog</h4>
            </div>
            <div class="card-body">
                <div id="headingOne" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false"
                     aria-controls="collapseOne" role="button">
                    <h5>2.0.1 <i class="text-sm fa-solid fa-chevron-down"></i></h5>
                </div>
                <div id="collapseOne" class="collapse pt-1" aria-labelledby="headingOne" data-parent="#cardAccordion">
                    <div class="ms-4">
                        <span class="badge bg-secondary">Fix</span>
                        <ul>
                            <li>Responsive template for Dashboard</li>
                            <li>A center div useless</li>
                        </ul>
                        <span class="badge bg-secondary">Add</span>
                        <ul>
                            <li>2nd Dropdown for menu</li>
                            <li>Spanish language</li>
                        </ul>
                        <span class="badge bg-secondary">Remove</span>
                        <ul>
                            <li>Paypal payement</li>
                        </ul>
                    </div>
                </div>

                <div id="headingTwo" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                     aria-controls="collapseOne" role="button">
                    <h5>2.0.0 <i class="text-sm fa-solid fa-chevron-down"></i></h5>
                </div>
                <div id="collapseTwo" class="collapse pt-1" aria-labelledby="headingTwo" data-parent="#cardAccordion">
                    <div class="ms-4">
                        <span class="badge bg-secondary">Create</span>
                        <ul>
                            <li>The best CMS ever seen</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Vertically Centered modal Modal -->
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
                <button type="button" class="btn btn-light-primary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Annuler</span>
                </button>
                <button type="button" class="btn btn-danger ml-1" data-bs-dismiss="modal">
                    <i class="bx bx-check d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Confirmer</span>
                </button>
            </div>
        </div>
    </div>
</div>