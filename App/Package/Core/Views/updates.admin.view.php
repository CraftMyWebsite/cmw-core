<?php

use CMW\Manager\Lang\LangManager;
use CMW\Manager\Updater\UpdatesManager;

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
                    <?php if (UpdatesManager::getVersion() !== UpdatesManager::getCmwLatest()->value) {
                        echo "<b class='text-danger'>" . UpdatesManager::getVersion() . "</b>";
                    } else {
                        echo "<b class='text-success'>" . UpdatesManager::getVersion() . "</b>";
                    }
                    ?>
                </p>
                <p>Dernière version : <b><?= UpdatesManager::getCmwLatest()->value ?></b></p>
                <?php if (UpdatesManager::checkNewUpdateAvailable()): ?>
                    <div class="buttons text-center">
                        <a href="cms/update" type="button" class="btn btn-primary">Mettre à jours</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-9">
        <div class="card">
            <div class="card-header">
                <h4>Notes de version</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="card-in-card">
                            <h4>Dernières verion</h4>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="card-in-card">
                            <h4>Version prècedentes</h4>
                        </div>
                    </div>
                </div>
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
