<?php

use CMW\Controller\Core\CoreController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Utils\Date;
use CMW\Utils\Website;

Website::setTitle(LangManager::translate('core.dashboard.title'));
Website::setDescription(LangManager::translate('core.dashboard.desc'));

$needUpdate = UpdatesManager::checkNewUpdateAvailable();
?>
<div class="space-y-2">
    <div class="alert-warning">
        <?= LangManager::translate('core.dashboard.alpha') ?>
    </div>

    <?php if ($needUpdate): ?>
        <div class="alert-danger">
            <?= LangManager::translate('core.dashboard.updateWarning') ?>
        </div>
    <?php endif; ?>
</div>


<h3><i class="fa-solid fa-chart-pie"></i> <?= LangManager::translate('core.dashboard.title') ?></h3>

<div class="grid-4">
    <div class="card col-span-3">
        <h6><?= LangManager::translate('core.dashboard.numbers_views') ?></h6>
        <div class="tab-menu">
            <ul class="tab-horizontal" data-tabs-toggle="#tab-content-1">
                <li>
                    <button data-tabs-target="#tab1" role="tab">
                        <?= LangManager::translate('core.dashboard.days') ?>
                    </button>
                </li>
                <li>
                    <button data-tabs-target="#tab2" role="tab">
                        <?= LangManager::translate('core.dashboard.weeks') ?>
                    </button>
                </li>
                <li>
                    <button data-tabs-target="#tab3" role="tab">
                        <?= LangManager::translate('core.dashboard.months') ?>
                    </button>
                </li>
            </ul>
        </div>
        <div id="tab-content-1">
            <div class="tab-content" id="tab1" style="height: 350px">
                <div id="daily-chart" class="loader"></div>
            </div>
            <div class="tab-content" id="tab2" style="height: 350px">
                <div id="weekly-chart" class="loader"></div>
            </div>
            <div class="tab-content" id="tab3" style="height: 350px">
                <div id="monthly-chart" class="loader"></div>
            </div>
        </div>
    </div>
    <div class="grid grid-2 lg:block lg:space-y-0 mt-4 lg:mt-0">
        <div class="card text-center">
            <div class="center-flex items-center gap-6 py-4">
                <i class="w-24 text-3xl fa-solid fa-user rounded-lg p-3 text-white"
                   style="background-color: #9694FF"></i>
                <div class="w-full lg:w-1/2 mt-2 lg:mt-0">
                    <p class="text-muted font-semibold">
                        <?= LangManager::translate('core.dashboard.total_member') ?>
                    </p>
                    <h6 class="font-extrabold mb-0" id="count-users">
                        <div class="animate-pulse w-2/3 h-3 bg-slate-200 mx-auto"></div>
                    </h6>
                </div>
            </div>
        </div>
        <div class="card text-center mt-0 lg:mt-4">
            <div class="center-flex items-center gap-6 py-4">
                <i class="w-24 fa-solid fa-calendar-day text-3xl rounded-lg p-3 text-white"
                   style="background-color: #57CAEB"></i>
                <div class="w-full lg:w-1/2 mt-2 lg:mt-0">
                    <p class="text-muted font-semibold">
                        <?= LangManager::translate('core.dashboard.daily_visits') ?>
                    </p>
                    <h6 class="font-extrabold mb-0" id="daily-visits">
                        <div class="animate-pulse w-2/3 h-3 bg-slate-200 mx-auto"></div>
                    </h6>
                </div>
            </div>
        </div>
        <div class="card text-center mt-0 lg:mt-4">
            <div class="center-flex items-center gap-6 py-4">
                <i class="w-24 fa-solid fa-calendar-days text-3xl rounded-lg p-3 text-white"
                   style="background-color: #5DDAB4"></i>
                <div class="w-full lg:w-1/2 mt-2 lg:mt-0">
                    <p class="text-muted font-semibold">
                        <?= LangManager::translate('core.dashboard.monthly_visits') ?>
                    </p>
                    <h6 class="font-extrabold mb-0" id="monthly-visits">
                        <div class="animate-pulse w-2/3 h-3 bg-slate-200 mx-auto"></div>
                    </h6>
                </div>
            </div>
        </div>
        <div class="card text-center mt-0 lg:mt-4">
            <div class="center-flex items-center gap-6 py-4">
                <i class="w-24  fa-regular fa-calendar text-3xl rounded-lg p-3 text-white"
                   style="background-color: #FF7976"></i>
                <div class="w-full lg:w-1/2 mt-2 lg:mt-0">
                    <p class="text-muted font-semibold">
                        <?= LangManager::translate('core.dashboard.total_visits') ?>
                    </p>
                    <h6 class="font-extrabold mb-0 " id="all-visits">
                        <div class="animate-pulse w-2/3 h-3 bg-slate-200 mx-auto"></div>
                    </h6>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="space-y-4 mt-4">
    <?php CoreController::getInstance()->getPackagesDashboardElements(); ?>
</div>


<script async>
    const generateMetrics = async () => {
        fetch('/cmw-admin/api/internal/dashboard/users/count').then(response => response.json()).then(updateUsersCount)
        fetch('/cmw-admin/api/internal/dashboard/visits').then(response => response.json()).then(updateVisitsMetrics)
        fetch('/cmw-admin/api/internal/dashboard/charts').then(response => response.json()).then(generateCharts)
    }

    const generateCharts = (metrics) => {
        const dailyVisitsData = Object.values(metrics.daily_visits);
        const weeklyVisitsData = Object.values(metrics.weekly_visits);
        const monthlyVisitsData = Object.values(metrics.monthly_visits);

        let daily_options = {
            series: [{
                name: 'Visites',
                data: dailyVisitsData
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
                data: weeklyVisitsData
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
                data: monthlyVisitsData
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

        const dailyChartElement = document.getElementById("daily-chart");
        const weeklyChartElement = document.getElementById("weekly-chart");
        const monthlyChartElement = document.getElementById("monthly-chart");

        dailyChartElement.classList.remove('loader');
        weeklyChartElement.classList.remove('loader');
        monthlyChartElement.classList.remove('loader');

        const dailyChart = new ApexCharts(dailyChartElement, daily_options);
        const weeklyChart = new ApexCharts(weeklyChartElement, weekly_options);
        const monthlyChart = new ApexCharts(monthlyChartElement, monthly_options);

        dailyChart.render();
        weeklyChart.render();
        monthlyChart.render();
    }

    const updateVisitsMetrics = (visits) => {
        const daily = document.getElementById('daily-visits');
        const monthly = document.getElementById('monthly-visits');
        const all = document.getElementById('all-visits');

        daily.innerText = visits.daily;
        monthly.innerText = visits.monthly;
        all.innerText = visits.all;
    }

    const updateUsersCount = (users) => {
        const count = document.getElementById('count-users');
        count.innerText = users.count;
    }

    document.addEventListener('DOMContentLoaded', () => {
        generateMetrics();
    });
</script>