<?php

use CMW\Controller\Core\CoreController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Metrics\VisitsMetricsManager;
use CMW\Model\Users\UsersModel;
use CMW\Model\Core\CoreModel;
use CMW\Utils\Utils;
use CMW\Utils\Website;

Website::setTitle(LangManager::translate("core.dashboard.title"));
Website::setDescription(LangManager::translate("core.dashboard.desc"));


/* @var array $monthlyVisits */
/* @var array $dailyVisits */
/* @var array $weeklyVisits */
/* @var array $registers */
?>
<div class="alert alert-warning">Veuillez noter que CraftMyWebsite2 est actuellement en phase alpha et n'est pas encore achevé. <br>Son utilisation en environnement de production est fortement déconseillée. Des fonctionnalités clés peuvent manquer ou ne pas fonctionner comme prévu. <br><b>Pendant cette phase, des réinstallations complètes du système pourront être nécessaires.</b> <br>Nous vous remercions de votre compréhension et de votre patience pendant que nous travaillons sur CraftMyWebsite.</div>

<div class="d-flex flex-wrap justify-content-between">
    <h3><i class="fa-solid fa-table-columns"></i> <span
                class="m-lg-auto"><?= LangManager::translate("core.dashboard.title") ?></span></h3>
</div>

<div class="page-content">
    <section class="row">
            <div class="row">
                <div class="col-lg-9">
                    <div class="card" >
                        <div class="card-header">
                            <h4><?= LangManager::translate("core.dashboard.numbers_views") ?></h4>
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="setting1-tab" data-bs-toggle="tab" href="#setting1"
                                       role="tab"
                                       aria-selected="true"><?= LangManager::translate("core.dashboard.days") ?></a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="setting2-tab" data-bs-toggle="tab" href="#setting2"
                                       role="tab"
                                       aria-selected="false"><?= LangManager::translate("core.dashboard.weeks") ?></a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="setting3-tab" data-bs-toggle="tab" href="#setting3"
                                       role="tab"
                                       aria-selected="false"><?= LangManager::translate("core.dashboard.months") ?></a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active py-2" id="setting1" role="tabpanel"
                                     aria-labelledby="setting1-tab">
                                    <div id="daily-chart">
                                    </div>
                                </div>
                                <div class="tab-pane fade py-2" id="setting2" role="tabpanel"
                                     aria-labelledby="setting2-tab">
                                    <div id="weekly-chart">
                                    </div>
                                </div>
                                <div class="tab-pane fade py-2" id="setting3" role="tabpanel"
                                     aria-labelledby="setting3-tab">
                                    <div id="monthly-chart">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




                <div class="col-sm-6 col-xl-3 text-center">
                    <div class="card">
                        <div class="card-body px-4">
                            <div class="row">
                                <div class="col-2 col-sm-4">
                                    <div class="stats-icon purple mb-2">
                                        <i class="fa-solid fa-users"></i>
                                    </div>
                                </div>
                                <div class="col-10 col-sm-8">
                                    <h6 class="text-muted font-semibold"><?= LangManager::translate("core.dashboard.total_member") ?></h6>
                                    <h6 class="font-extrabold mb-0"><?= UsersModel::getInstance()->countUsers() ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body px-4">
                            <div class="row">
                                <div class="col-2 col-sm-4">
                                    <div class="stats-icon blue mb-2">
                                        <i class="fa-solid fa-calendar-day"></i>
                                    </div>
                                </div>
                                <div class="col-10 col-sm-8">
                                    <h6 class="text-muted font-semibold"><?= LangManager::translate("core.dashboard.daily_visits") ?></h6>
                                    <h6 class="font-extrabold mb-0"><?= (new VisitsMetricsManager())->getVisitsNumber("day") ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body px-4">
                            <div class="row">
                                <div class="col-2 col-sm-4">
                                    <div class="stats-icon green mb-2">
                                        <i class="fa-solid fa-calendar-days"></i>
                                    </div>
                                </div>
                                <div class="col-10 col-sm-8">
                                    <h6 class="text-muted font-semibold"><?= LangManager::translate("core.dashboard.monthly_visits") ?></h6>
                                    <h6 class="font-extrabold mb-0"><?= (new VisitsMetricsManager())->getVisitsNumber("monthly") ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body px-4">
                            <div class="row">
                                <div class="col-2 col-sm-4">
                                    <div class="stats-icon red mb-2">
                                        <i class="fa-regular fa-calendar"></i>
                                    </div>
                                </div>
                                <div class="col-10 col-sm-8">
                                    <h6 class="text-muted font-semibold"><?= LangManager::translate("core.dashboard.total_visits") ?></h6>
                                    <h6 class="font-extrabold mb-0"><?= (new VisitsMetricsManager())->getVisitsNumber("all") ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3 text-center">

                </div>
                <div class="col-sm-6 col-xl-3  text-center">

                </div>
                <div class="col-sm-6 col-xl-3 text-center">

                </div>




            </div>

        <?php CoreController::getInstance()->getPackagesDashboardElements(); ?>

    </section>
</div>

<script>
    let daily_options = {
        series: [{
            name: 'Visites',
            data: [<?php foreach ($dailyVisits as $dailyVisit):
                echo json_encode($dailyVisit, JSON_THROW_ON_ERROR) . ",";
            endforeach;?>]
        }],
        chart: {
            height: 350,
            type: 'area'
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: <?= json_encode(Utils::getPastDays(17), JSON_THROW_ON_ERROR) ?>,
            labels: {
                show: true,
                rotate: -45,
                rotateAlways: true,
            },
        },
    };
    let weekly_options = {
        series: [{
            name: 'Visites',
            data: [<?php foreach ($weeklyVisits as $weeklyVisit):
                echo json_encode($weeklyVisit, JSON_THROW_ON_ERROR) . ",";
            endforeach;?>]
        }],
        chart: {
            height: 350,
            type: 'area'
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: <?= json_encode(Utils::getPastWeeks(17), JSON_THROW_ON_ERROR) ?>,
            labels: {
                show: true,
                rotate: -45,
                rotateAlways: true,
            },
        },
    };
    let monthly_options = {
        series: [{
            name: 'Visites',
            data: [<?php foreach ($monthlyVisits as $monthlyVisit):
                echo json_encode($monthlyVisit, JSON_THROW_ON_ERROR) . ",";
            endforeach;?>]
        }],
        chart: {
            height: 350,
            type: 'area'
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: <?= json_encode(Utils::getPastMonths(12), JSON_THROW_ON_ERROR) ?>,
            labels: {
                show: true,
                rotate: -45,
                rotateAlways: true,
            },
        },
    };

    let dailyChart = new ApexCharts(document.querySelector("#daily-chart"), daily_options);
    let weeklyChart = new ApexCharts(document.querySelector("#weekly-chart"), weekly_options);
    let monthlyChart = new ApexCharts(document.querySelector("#monthly-chart"), monthly_options);
    dailyChart.render();
    weeklyChart.render();
    monthlyChart.render();
</script>