<?php 
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Metrics\VisitsMetricsManager;
use CMW\Model\Users\UsersModel;
use CMW\Model\Core\CoreModel;
use CMW\Utils\Website;

$title = LangManager::translate("core.dashboard.title");
$description = LangManager::translate("core.dashboard.desc"); 
?>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-table-columns"></i> <span class="m-lg-auto"><?= LangManager::translate("core.dashboard.title") ?></span></h3>
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
                                    <h6 class="text-muted font-semibold"><?= LangManager::translate("core.dashboard.total_member") ?></h6>
                                    <h6 class="font-extrabold mb-0"><?= (new UsersModel())->countUsers() ?></h6>
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
                                    <h6 class="text-muted font-semibold"><?= LangManager::translate("core.dashboard.best_views") ?></h6>
                                    <h6 class="font-extrabold mb-0"><?= (new VisitsMetricsManager())->getMonthlyBestVisits() ?></h6>
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
                                    <h6 class="text-muted font-semibold"><?= LangManager::translate("core.dashboard.monthly_visits") ?></h6>
                                    <h6 class="font-extrabold mb-0"><?= (new VisitsMetricsManager())->getVisitsNumber("monthly") ?></h6>
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
                                    <h6 class="text-muted font-semibold"><?= LangManager::translate("core.dashboard.total_visits") ?></h6>
                                    <h6 class="font-extrabold mb-0"><?= (new VisitsMetricsManager())->getVisitsNumber("all") ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                      <!-- //TODO Visits chartjs -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><?= LangManager::translate("core.dashboard.numbers_views") ?></h4>
                        </div>
                        <div class="card-body">
                            <canvas id="dashboardVisits" width="710" height="200" style="display: block; box-sizing: border-box; height: 355px; width: 710px;"></canvas>
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
                            <img src="<?= UsersModel::getCurrentUser()->getUserPicture()->getImageLink() ?>" alt="<?= LangManager::translate("users.users.image.image_alt", ['username' => UsersModel::getCurrentUser()->getPseudo()]) ?>">
                        </div>
                        <div class="ms-3 name">
                            <h5 class="font-bold"><?= UsersModel::getCurrentUser()->getPseudo() ?></h5>
                            <h6 class="text-muted mb-0"><?= LangManager::translate("core.dashboard.welcome") ?> <span style="text-transform: lowercase;"><?= UsersModel::getCurrentUser()->getHighestRole()->getName() ?></span></h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5><?= LangManager::translate("core.dashboard.site_info") ?></h5>
                </div>
                <div class="card-body ">
                    <p><?= LangManager::translate("core.dashboard.name") ?> <b><?= CoreModel::getOptionValue("name") ?></b></p>
                    <p><?= LangManager::translate("core.dashboard.description") ?> <span class="text-muted"><?= CoreModel::getOptionValue("description") ?></span></p>
                        <div class="px-4 text-center">
                            <a href="<?= Website::getProtocol() . '://' . $_SERVER['SERVER_NAME'] . getenv("PATH_SUBFOLDER") . "cmw-admin/configuration" ?>" class="btn btn-primary float-right"><?= LangManager::translate("core.dashboard.edit") ?><br></a>
                        </div>
                </div>
            </div>
        </div>
    </section>
</div>