<?php 
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Utils;
use CMW\Model\Users\UsersModel;
use CMW\Model\Core\CoreModel;
$title = LangManager::translate("core.dashboard.title");
$description = LangManager::translate("core.dashboard.desc"); 
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-table-columns"></i> <span class="m-lg-auto">Tableau de bord</span></h3>
</div>

<div class="page-content">
    <section class="row">
        <div class="col-12 col-lg-9">
            <div class="row">
                <div class="col-6 col-lg-3 col-md-6 text-center">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="stats-icon purple mb-2">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <h6 class="text-muted font-semibold">Membres totaux</h6>
                                    <h6 class="font-extrabold mb-0">4</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6 text-center">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="stats-icon blue mb-2">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <h6 class="text-muted font-semibold">Record de visites</h6>
                                    <h6 class="font-extrabold mb-0">80000</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6 text-center">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="stats-icon green mb-2">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <h6 class="text-muted font-semibold">Membres totaux</h6>
                                    <h6 class="font-extrabold mb-0">4</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 col-md-6 text-center">
                    <div class="card">
                        <div class="card-body px-4 py-4-5">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="stats-icon red mb-2">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <h6 class="text-muted font-semibold">Membres totaux</h6>
                                    <h6 class="font-extrabold mb-0">4</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                      <!--Visite chartjs-->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Nombres de visites</h4>
                        </div>
                        <div class="card-body">
                            <div id="chart-profile-visit"></div>
                        </div>
                    </div>
                </div>
            </div>     
        </div>

        <div class="col-12 col-lg-3">
            <div class="card">
                <div class="card-body py-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-xl">
                            <img src="<?= UsersModel::getCurrentUser()->getUserPicture()->getImageLink() ?>" alt="<?= LangManager::translate("users.users.image.image_alt", ['username' => UsersModel::getCurrentUser()->getUsername()]) ?>">
                        </div>
                        <div class="ms-3 name">
                            <h5 class="font-bold"><?= UsersModel::getCurrentUser()->getUsername() ?></h5>
                            <h6 class="text-muted mb-0">Bienvenue <span style="text-transform: lowercase;"><?= UsersModel::getCurrentUser()->getHighestRole()->getName() ?></span></h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5>Information du site</h5>
                </div>
                <div class="card-body ">
                    <p>Nom : <b><?= CoreModel::getOptionValue("name") ?></b></p>
                    <p>Description :<span class="text-muted"><?= CoreModel::getOptionValue("description") ?></span></p>
                        <div class="px-4 text-center">
                            <a href="<?= Utils::getHttpProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . "cmw-admin/configuration" ?>" class="btn btn-primary float-right">Modifier ces informations<br></a>
                        </div>
                </div>
            </div>
        </div>
    </section>
</div>