<?php

namespace CMW\Manager\Metrics;

use CMW\Manager\Database\DatabaseManager;
use CMW\Router\Route;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\ExpectedValues;

class VisitsMetricsManager extends DatabaseManager
{
    private int $maxLines = 50; // Variable data ?
    private string $fileName;

    public function __construct()
    {
        $this->fileName = Utils::getEnv()->getValue("DIR") . "App/Storage/Visits/history.log";
    }

    public function registerVisit(Route $route): void
    {
        $package = explode(".", $route->getName())[0] ?? null;

        $isAdmin = str_starts_with($route->getPath(), "cmw-Admin/") ?: 0;

        $path = $route->getPath() === '' ? '/' : $route->getPath();

        $_SESSION['latestVisitPath'] = $path;

        if ($this->isDuplicateVisit($path) || $this->isAdminVisit($path)){
            return;
        }

        $data = Utils::getClientIp() . "," . date('Y-m-d H:i:s') . "," . $path . "," . $package . "," . http_response_code() . "," . $isAdmin;

        $this->saveLogFile($data);

        if ($this->isLogsAreFull()) {
            $this->sendLogToDatabase();
        }
    }

    private function isDuplicateVisit(string $currentPath): bool
    {
        return $_SESSION['latestVisitPath'] === $currentPath;
    }

    private function isAdminVisit(string $currentPath): bool
    {
        return str_contains($currentPath, 'cmw-admin');
    }

    private function saveLogFile(string $data): void
    {
        if (stream_resolve_include_path($this->fileName)) {
            file_put_contents($this->fileName, $data . PHP_EOL, FILE_APPEND | LOCK_EX | FILE_SKIP_EMPTY_LINES);
        } else {
            fopen($this->fileName, 'wb');
            $this->saveLogFile($data);
        }
    }

    private function isLogsAreFull(): bool
    {
        return $this->getFileLineNumber() >= $this->maxLines;
    }

    private function getFileLineNumber(): int
    {
        if (stream_resolve_include_path($this->fileName)) {
            return count(file($this->fileName));
        }

        return 0;
    }

    private function sendLogToDatabase(): void
    {

        $sql = "INSERT INTO cmw_visits (visits_ip, visits_date, visits_path, visits_package, visits_code, visits_is_admin) VALUES ";
        foreach ($this->getLogData() as $line) {
            $res = explode(",", $line);
            $sql .= "('$res[0]','$res[1]','$res[2]','$res[3]','$res[4]','$res[5]'),";
        }

        $db = self::getInstance();
        $db->query(mb_substr($sql, 0, -1));

        //Delete old file
        unlink($this->fileName);
    }

    private function getLogData(): array
    {
        return file($this->fileName);
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

            $var = array(
                "range_start" => $rangeStart,
                "range_finish" => $rangeFinish
            );

            $sql = "SELECT COUNT(visits_id) AS `result` FROM cmw_visits WHERE visits_date BETWEEN (:range_start) AND (:range_finish)";

            $db = self::getInstance();
            $req = $db->prepare($sql);
            $res = $req->execute($var);

        else:
            $sql = "SELECT COUNT(visits_id) AS `result` FROM cmw_visits";

            $db = self::getInstance();
            $req = $db->prepare($sql);
            $res = $req->execute();

        endif;

        if ($res) {
            return $req->fetch()['result'] + $this->getFileLineNumber();
        }

        return $this->getFileLineNumber();
    }

    /**
     * @return int
     */
    public function getMonthlyBestVisits(): int
    {
        $sql = "SELECT DATE_FORMAT(`visits_date`, '%M') AS `month`, COUNT(visits_id) count
                FROM cmw_visits
                GROUP BY DATE_FORMAT(`visits_date`, '%M') ORDER BY month DESC LIMIT 1";

        $db = self::getInstance();

        $req = $db->query($sql);

        if (!$req){
            return 0;
        }


        return $req->fetch()['count'] ?? 0;
    }

}