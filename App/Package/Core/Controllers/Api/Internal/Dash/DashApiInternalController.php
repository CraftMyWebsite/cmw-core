<?php

namespace CMW\Controller\Core\Api\Internal\Dash;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Metrics\VisitsMetricsManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Model\Users\UsersModel;
use function json_encode;
use const JSON_THROW_ON_ERROR;

/**
 * Class: @DashApiInternalController
 * @package Core
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/controllers
 */
class DashApiInternalController extends AbstractController
{
    #[Link('/dashboard/charts', Link::GET, [], '/cmw-admin/api/internal')]
    private function getDashboardPageCharts(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard');

        $monthlyVisits = VisitsMetricsManager::getInstance()->getPastMonthsVisits(12);
        $dailyVisits = VisitsMetricsManager::getInstance()->getPastDaysVisits(17);
        $weeklyVisits = VisitsMetricsManager::getInstance()->getPastWeeksVisits(17);

        $data = [
            'monthly_visits' => $monthlyVisits,
            'daily_visits' => $dailyVisits,
            'weekly_visits' => $weeklyVisits,
        ];

        print json_encode($data, JSON_THROW_ON_ERROR);
    }

    #[Link('/dashboard/visits', Link::GET, [], '/cmw-admin/api/internal')]
    private function getDashboardPageVisits(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard');

        $daily = VisitsMetricsManager::getInstance()->getVisitsNumber('day');
        $monthly = VisitsMetricsManager::getInstance()->getVisitsNumber('monthly');
        $all = VisitsMetricsManager::getInstance()->getVisitsNumber('all');

        $data = [
            'daily' => $daily,
            'monthly' => $monthly,
            'all' => $all,
        ];

        print json_encode($data, JSON_THROW_ON_ERROR);
    }

    #[Link('/dashboard/users/count', Link::GET, [], '/cmw-admin/api/internal')]
    private function getDashboardUsersCount(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard');

        $count = UsersModel::getInstance()->countUsers();

        print json_encode(['count' => $count], JSON_THROW_ON_ERROR);
    }
}
