<?php

namespace CMW\Manager\Metrics;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Permission\PermissionManager;
use CMW\Manager\Router\Route;
use CMW\Utils\File;
use CMW\Utils\Website;
use JetBrains\PhpStorm\ExpectedValues;

class VisitsMetricsManager extends AbstractManager
{
    private int $maxLines = 50; // Variable data ?
    private string $filePath;
    private string $dirStorage;

    public function __construct()
    {
        $this->dirStorage = EnvManager::getInstance()->getValue("DIR") . "App/Storage/Visits";
        $this->filePath = "$this->dirStorage/history.log";
    }

    public function registerVisit(Route $route): void
    {
        $package = explode(".", $route->getName())[0] ?? null;

        $isAdmin = str_starts_with($route->getPath(), "cmw-admin/") ?: 0;

        $path = $route->getPath() === '' ? '/' : $route->getPath();

        if ($this->isDuplicateVisit($path) || $this->isAdminVisit($path) || $this->isErrorPage($path)) {
            return;
        }

        $_SESSION['latestVisitPath'] = $path;

        $data = Website::getClientIp() . "," . date('Y-m-d H:i:s') . "," . $path . "," . $package . "," . http_response_code() . "," . $isAdmin;

        //If we don't have file perms, we ignore temp file writing
        if (!$this->checkPermissions()) {
            $this->sendLogToDatabase();
            return;
        }

        $this->saveLogFile($data);
        if ($this->isLogsAreFull()) {
            $this->sendLogToDatabase();
        }
    }

    private function isDuplicateVisit(string $currentPath): bool
    {
        if (!isset($_SESSION['latestVisitPath'])) {
            return false;
        }
        return $_SESSION['latestVisitPath'] === $currentPath;
    }

    private function isAdminVisit(string $currentPath): bool
    {
        return str_contains($currentPath, 'cmw-admin');
    }

    private function isErrorPage(string $currentPath): bool
    {
        return str_contains($currentPath, 'geterror');
    }

    private function saveLogFile(string $data): void
    {
        if (stream_resolve_include_path($this->filePath)) {
            File::write($this->filePath, $data . PHP_EOL, FILE_APPEND | LOCK_EX | FILE_SKIP_EMPTY_LINES);
        } else {
            fopen($this->filePath, 'wb');
            $this->saveLogFile($data);
        }
    }

    private function isLogsAreFull(): bool
    {
        return $this->getFileLineNumber() >= $this->maxLines;
    }

    private function getFileLineNumber(): int
    {
        if (stream_resolve_include_path($this->filePath)) {
            return count(file($this->filePath));
        }

        return 0;
    }

    private function sendLogToDatabase(): void
    {
        $logs = $this->getLogData();

        if (empty($logs)) {
            return;
        }

        $sql = "INSERT INTO cmw_visits (visits_ip, visits_date, visits_path, visits_package, visits_code, visits_is_admin) VALUES ";
        foreach ($logs as $line) {
            $res = explode(",", $line);
            $sql .= "('$res[0]','$res[1]','$res[2]','$res[3]','$res[4]','$res[5]'),";
        }

        $db = DatabaseManager::getInstance();
        $db->query(mb_substr($sql, 0, -1));

        //Clean old file
        File::write($this->filePath, "");
    }

    private function getLogData(): array
    {
        return File::readArray($this->filePath);
    }

    public function getVisitsNumber(#[ExpectedValues(["all", "monthly", "week", "day", "hour"])] $period): ?int
    {
        $rangeStart = null;
        $rangeFinish = null;

        if ($period === "monthly" || $period === "week" || $period === "day" || $period === "hour"):
            switch ($period):
                case "monthly":
                    $rangeStart = date("Y-m-d 00:00:00", strtotime("first day of this month"));
                    $rangeFinish = date("Y-m-d 00:00:00", strtotime("last day of this month"));
                    break;
                case "week":
                    $rangeStart = date("Y-m-d 00:00:00", strtotime("monday this week"));
                    $rangeFinish = date("Y-m-d 00:00:00", strtotime("sunday this week"));
                    break;
                case "day":
                    $rangeStart = date("Y-m-d 00:00:00");
                    $rangeFinish = date("Y-m-d 23:59:59");
                    break;
                case "hour":
                    $rangeStart = date("Y-m-d h:00:00");
                    $rangeFinish = date("Y-m-d h:00:00", strtotime("+1 hour"));
                    break;
            endswitch;

            $var = [
                "range_start" => $rangeStart,
                "range_finish" => $rangeFinish,
            ];

            $sql = "SELECT COUNT(DISTINCT visits_ip) AS `result` FROM cmw_visits WHERE visits_date BETWEEN (:range_start) AND (:range_finish)";

            $db = DatabaseManager::getInstance();
            $req = $db->prepare($sql);
            $res = $req->execute($var);

        else:
            $sql = "SELECT COUNT(DISTINCT visits_ip) AS `result` FROM cmw_visits";

            $db = DatabaseManager::getInstance();
            $req = $db->prepare($sql);
            $res = $req->execute();

        endif;

        if ($res) {
            return $req->fetch()['result'] + $this->getFileLineNumber();
        }

        return $this->getFileLineNumber();
    }

    /**
     * @param string $rangeStart
     * @param string $rangeFinish
     * @return int
     */
    public function getDataVisits(string $rangeStart, string $rangeFinish): int
    {
        $var = [
            "range_start" => $rangeStart,
            "range_finish" => $rangeFinish,
        ];

        $sql = "SELECT COUNT(DISTINCT visits_ip) AS `result` FROM cmw_visits WHERE visits_date BETWEEN (:range_start) AND (:range_finish)";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $res = $req->execute($var);

        if (!$res) {
            return 0;
        }

        return $req->fetch()['result'] ?? 0;
    }

    /**
     * @return int
     */
    public function getMonthlyBestVisits(): int
    {
        $sql = "SELECT DATE_FORMAT(`visits_date`, '%M') AS `month`, COUNT(DISTINCT visits_ip) count
                FROM cmw_visits
                GROUP BY DATE_FORMAT(`visits_date`, '%M') ORDER BY month DESC LIMIT 1";

        $db = DatabaseManager::getInstance();

        $req = $db->query($sql);

        if (!$req) {
            return 0;
        }


        return $req->fetch()['count'] ?? 0;
    }

    /**
     * @param int $pastMonths
     * @return array
     */
    public function getPastMonthsVisits(int $pastMonths): array
    {
        $currentMonth = idate('m');

        $toReturn = [];

        for ($i = 0; $i < $pastMonths; $i++) {
            $targetMonth = idate("m", strtotime("-$i months"));
            $targetMonthTranslate = LangManager::translate("core.months.$targetMonth");

            $rangeStart = date("Y-m-d 00:00:00", strtotime("first day of -$i months"));
            $rangeFinish = date("Y-m-d 23:59:59", strtotime("last day of -$i months"));

            $toReturn[$targetMonthTranslate] = $this->getDataVisits($rangeStart, $rangeFinish);

            if ($targetMonth === $currentMonth) {
                $toReturn[$targetMonthTranslate] += $this->getFileLineNumber();
            }

        }
        return array_reverse($toReturn);
    }

    /**
     * @param int $pastDays
     * @return array
     */
    public function getPastDaysVisits(int $pastDays): array
    {
        $currentDay = idate("d");

        $toReturn = [];

        for ($i = 0; $i < $pastDays; $i++) {
            $targetDay = date("d", strtotime("-$i days"));

            if ($targetDay === $currentDay) {
                $rangeStart = date("Y-m-d 00:00:00", strtotime("-$i days"));
                $rangeFinish = date("Y-m-d 23:59:59", strtotime("-$i days"));

                $dataVisits = $this->getDataVisits($rangeStart, $rangeFinish);
                $toReturn[] = $dataVisits + $this->getFileLineNumber();
            } else {
                $rangeStart = date("Y-m-d 00:00:00", strtotime("-$i days"));
                $rangeFinish = date("Y-m-d 23:59:59", strtotime("-$i days"));
                $toReturn[] = $this->getDataVisits($rangeStart, $rangeFinish);
            }
        }

        return array_reverse($toReturn);
    }

    /**
     * @param int $pastWeeks
     * @return array
     */
    public function getPastWeeksVisits(int $pastWeeks): array
    {
        $currentWeeks = idate('W');

        $toReturn = [];

        for ($i = 0; $i < $pastWeeks; $i++) {
            $targetWeek = idate("W", strtotime("-$i weeks"));


            $rangeStart = date("Y-m-d 00:00:00", strtotime("-$i monday this week"));
            $rangeFinish = date("Y-m-d 23:59:59", strtotime("-$i sunday this week"));

            $toReturn[] = $this->getDataVisits($rangeStart, $rangeFinish);

            if ($targetWeek === $currentWeeks) {
                $toReturn[] = $this->getDataVisits($rangeStart, $rangeFinish) + $this->getFileLineNumber();
            }

        }
        return array_reverse($toReturn);
    }

    /**
     * @return bool
     */
    private function checkPermissions(): bool
    {
        return PermissionManager::canCreateFile($this->dirStorage);
    }
}