<?php 
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Metrics\VisitsMetricsManager;
use CMW\Model\Users\UsersModel;
use CMW\Model\Core\CoreModel;
use CMW\Utils\Log;
use CMW\Utils\Utils;
use CMW\Utils\Website;

$title = LangManager::translate("core.dashboard.title");
$description = LangManager::translate("core.dashboard.desc");


/* @var array $monthlyVisits */
/* @var array $dailyVisits */
/* @var array $weeklyVisits */
/* @var array $registers */
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
                                    <h6 class="font-extrabold mb-0"><?= UsersModel::getInstance()->countUsers() ?></h6>
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
                                        <i class="fa-solid fa-calendar-day"></i>
                                    </div>
                                </div>
                                <div class="col-sm-8">
                                    <h6 class="text-muted font-semibold"><?= LangManager::translate("core.dashboard.daily_visits") ?></h6>
                                    <h6 class="font-extrabold mb-0"><?= (new VisitsMetricsManager())->getVisitsNumber("day") ?></h6>
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
                                        <i class="fa-solid fa-calendar-days"></i>
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
                                        <i class="fa-regular fa-calendar"></i>
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
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" id="setting1-tab" data-bs-toggle="tab" href="#setting1" role="tab"
                                       aria-selected="true">Jours</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="setting2-tab" data-bs-toggle="tab" href="#setting2" role="tab"
                                       aria-selected="false">Semaine</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" id="setting3-tab" data-bs-toggle="tab" href="#setting3" role="tab"
                                       aria-selected="false">Mois</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active py-2" id="setting1" role="tabpanel" aria-labelledby="setting1-tab">
                                    <canvas id="dashboardDailyVisits" width="710" height="200" style="display: block; box-sizing: border-box; height: 355px; width: 710px;"></canvas>
                                </div>
                                <div class="tab-pane fade py-2" id="setting2" role="tabpanel" aria-labelledby="setting2-tab">
                                    <canvas id="dashboardWeeklyVisits" width="710" height="200" style="display: block; box-sizing: border-box; height: 355px; width: 710px;"></canvas>
                                </div>
                                <div class="tab-pane fade py-2" id="setting3" role="tabpanel" aria-labelledby="setting3-tab">
                                    <canvas id="dashboardMonthlyVisits" width="710" height="200" style="display: block; box-sizing: border-box; height: 355px; width: 710px;"></canvas>
                                </div>
                            </div>
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

<script>
    const dashboardMonthlyVisits = document.getElementById('dashboardMonthlyVisits');
    const dashboardMonthlyVisitsLabels = <?= json_encode(Utils::getPastMonths(5), JSON_THROW_ON_ERROR) ?>;

    const dashboardDailyVisits = document.getElementById('dashboardDailyVisits');
    const dashboardDailyVisitsLabels = <?= json_encode(Utils::getPastDays(15), JSON_THROW_ON_ERROR) ?>;

    const dashboardWeeklyVisits = document.getElementById('dashboardWeeklyVisits');
    const dashboardWeeklyVisitsLabels = <?= json_encode(Utils::getPastWeeks(4), JSON_THROW_ON_ERROR) ?>;


    const dashboardMonthlyVisitsData = {
        labels: dashboardMonthlyVisitsLabels,
        datasets: [
            {
                data: [
                    <?php foreach ($monthlyVisits as $monthlyVisit):
                        echo json_encode($monthlyVisit, JSON_THROW_ON_ERROR) . ",";
                    endforeach;?>
                ],
                label: '<?= LangManager::translate('core.dashboard.visits') ?>',
                backgroundColor: '#ab4057',
                borderWidth: 2,
                borderRadius: 10,
            },
            {
                label: '<?= LangManager::translate('core.dashboard.registers') ?>',
                data: [
                    <?php foreach ($registers as $register):
                    echo json_encode($register, JSON_THROW_ON_ERROR) . ",";
                endforeach;?>
                ],
                backgroundColor: '#bf30df',
                borderWidth: 2,
                borderRadius: 10,
            }
        ]
    };

    const dashboardDailyVisitsData = {
        labels: dashboardDailyVisitsLabels,
        datasets: [
            {
                data: [
                    <?php foreach ($dailyVisits as $dailyVisit):
                    echo json_encode($dailyVisit, JSON_THROW_ON_ERROR) . ",";
                endforeach;?>
                ],
                label: '<?= LangManager::translate('core.dashboard.visits') ?>',
                backgroundColor: '#ab4057',
                borderWidth: 2,
                borderRadius: 20,
            }
        ]
    };

    const dashboardWeeklyVisitsData = {
        labels: dashboardWeeklyVisitsLabels,
        datasets: [
            {
                data: [
                    <?php foreach ($weeklyVisits as $weeklyVisit):
                    echo json_encode($weeklyVisit, JSON_THROW_ON_ERROR) . ",";
                endforeach;?>
                ],
                label: '<?= LangManager::translate('core.dashboard.visits') ?>',
                backgroundColor: '#ab4057',
                borderWidth: 2,
                borderRadius: 10,
            }
        ]
    };

    new Chart(dashboardMonthlyVisits, {
        type: 'bar',
        data: dashboardMonthlyVisitsData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            }
        },
    });

    new Chart(dashboardDailyVisits, {
        type: 'bar',
        data: dashboardDailyVisitsData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            }
        },
    });

    new Chart(dashboardWeeklyVisits, {
        type: 'bar',
        data: dashboardWeeklyVisitsData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            }
        },
    });
</script>