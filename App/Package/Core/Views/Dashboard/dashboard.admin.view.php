<?php

use CMW\Controller\Core\CoreController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Metrics\VisitsMetricsManager;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Date;
use CMW\Utils\Website;

Website::setTitle(LangManager::translate('core.dashboard.title'));
Website::setDescription(LangManager::translate('core.dashboard.desc'));

/* @var array $monthlyVisits */
/* @var array $dailyVisits */
/* @var array $weeklyVisits */
/* @var array $registers */
/* @var bool $isUpToDate */
?>
<div class="alert-warning">
    <?= LangManager::translate('core.dashboard.alpha') ?>
</div>

<?php if (!$isUpToDate): ?>
    <div class="alert-danger mt-2">
        <?= LangManager::translate('core.dashboard.updateWarning') ?>
    </div>
<?php endif; ?>


<h3><i class="fa-solid fa-chart-pie"></i> <?= LangManager::translate('core.dashboard.title') ?></h3>

<div class="grid-4">
    <div class="card col-span-3">
        <h6><?= LangManager::translate('core.dashboard.numbers_views') ?></h6>
        <div class="tab-menu">
            <ul class="tab-horizontal" data-tabs-toggle="#tab-content-1">
                <li>
                    <button data-tabs-target="#tab1"
                            role="tab"><?= LangManager::translate('core.dashboard.days') ?></button>
                </li>
                <li>
                    <button data-tabs-target="#tab2"
                            role="tab"><?= LangManager::translate('core.dashboard.weeks') ?></button>
                </li>
                <li>
                    <button data-tabs-target="#tab3"
                            role="tab"><?= LangManager::translate('core.dashboard.months') ?></button>
                </li>
            </ul>
        </div>
        <div id="tab-content-1">
            <div class="tab-content" id="tab1">
                <div id="daily-chart"></div>
            </div>
            <div class="tab-content" id="tab2">
                <div id="weekly-chart"></div>
            </div>
            <div class="tab-content" id="tab3">
                <div id="monthly-chart"></div>
            </div>
        </div>
    </div>
    <div class="grid grid-2 lg:block lg:space-y-0 mt-4 lg:mt-0">
        <div class="card text-center">
            <div class="center-flex items-center gap-6 py-4">
                <i class="w-24 text-3xl fa-solid fa-user rounded-lg p-3 text-white"
                   style="background-color: #9694FF"></i>
                <div class="w-full lg:w-1/2 mt-2 lg:mt-0">
                    <p class="text-muted font-semibold"><?= LangManager::translate('core.dashboard.total_member') ?></p>
                    <h6 class="font-extrabold mb-0"><?= UsersModel::getInstance()->countUsers() ?></h6>
                </div>
            </div>
        </div>
        <div class="card text-center mt-0 lg:mt-4">
            <div class="center-flex items-center gap-6 py-4">
                <i class="w-24 fa-solid fa-calendar-day text-3xl rounded-lg p-3 text-white"
                   style="background-color: #57CAEB"></i>
                <div class="w-full lg:w-1/2 mt-2 lg:mt-0">
                    <p class="text-muted font-semibold"><?= LangManager::translate('core.dashboard.daily_visits') ?></p>
                    <h6 class="font-extrabold mb-0"><?= (new VisitsMetricsManager())->getVisitsNumber('day') ?></h6>
                </div>
            </div>
        </div>
        <div class="card text-center mt-0 lg:mt-4">
            <div class="center-flex items-center gap-6 py-4">
                <i class="w-24 fa-solid fa-calendar-days text-3xl rounded-lg p-3 text-white"
                   style="background-color: #5DDAB4"></i>
                <div class="w-full lg:w-1/2 mt-2 lg:mt-0">
                    <p class="text-muted font-semibold"><?= LangManager::translate('core.dashboard.monthly_visits') ?></p>
                    <h6 class="font-extrabold mb-0"><?= (new VisitsMetricsManager())->getVisitsNumber('monthly') ?></h6>
                </div>
            </div>
        </div>
        <div class="card text-center mt-0 lg:mt-4">
            <div class="center-flex items-center gap-6 py-4">
                <i class="w-24  fa-regular fa-calendar text-3xl rounded-lg p-3 text-white"
                   style="background-color: #FF7976"></i>
                <div class="w-full lg:w-1/2 mt-2 lg:mt-0">
                    <p class="text-muted font-semibold"><?= LangManager::translate('core.dashboard.total_visits') ?></p>
                    <h6 class="font-extrabold mb-0"><?= (new VisitsMetricsManager())->getVisitsNumber('all') ?></h6>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="space-y-4 mt-4">
    <?php CoreController::getInstance()->getPackagesDashboardElements(); ?>
</div>


<script>
    let daily_options = {
        series: [{
            name: 'Visites',
            data: [<?php foreach ($dailyVisits as $dailyVisit):
                echo json_encode($dailyVisit, JSON_THROW_ON_ERROR) . ',';
            endforeach; ?>]
        }],
        chart: {
            height: 350,
            type: 'area'
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: <?= json_encode(Date::getPastDays(17), JSON_THROW_ON_ERROR) ?>,
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
                echo json_encode($weeklyVisit, JSON_THROW_ON_ERROR) . ',';
            endforeach; ?>]
        }],
        chart: {
            height: 350,
            type: 'area'
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: <?= json_encode(Date::getPastWeeks(17), JSON_THROW_ON_ERROR) ?>,
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
                echo json_encode($monthlyVisit, JSON_THROW_ON_ERROR) . ',';
            endforeach; ?>]
        }],
        chart: {
            height: 350,
            type: 'area'
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: <?= json_encode(Date::getPastMonths(12), JSON_THROW_ON_ERROR) ?>,
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