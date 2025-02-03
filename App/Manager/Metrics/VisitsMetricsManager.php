<?php

namespace CMW\Manager\Metrics;

use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Permission\PermissionManager;
use CMW\Manager\Router\Route;
use CMW\Utils\Client;
use CMW\Utils\File;
use JetBrains\PhpStorm\ExpectedValues;
use PDO;
use function count;
use function date;
use function explode;
use function file;
use function fopen;
use function http_response_code;
use function mb_substr;
use function str_contains;
use function str_starts_with;
use function stream_resolve_include_path;
use function strtotime;
use const FILE_APPEND;
use const FILE_SKIP_EMPTY_LINES;
use const LOCK_EX;
use const PHP_EOL;

class VisitsMetricsManager
{
    private int $maxLines = 50;  // Variable data ?
    private string $filePath;
    private string $dirStorage;
    private static ?VisitsMetricsManager $instance = null;
    private ?array $cachedVisits = null;

    public function __construct()
    {
        $this->dirStorage = EnvManager::getInstance()->getValue('DIR') . 'App/Storage/Visits';
        $this->filePath = "$this->dirStorage/history.log";
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function registerVisit(Route $route): void
    {
        $package = explode('.', $route->getName())[0] ?? null;

        $isAdmin = str_starts_with($route->getPath(), 'cmw-admin/') ?: 0;

        $path = $route->getPath() === '' ? '/' : $route->getPath();

        if ($this->isDuplicateVisit($path) || $this->isAdminVisit($path) || $this->isErrorPage($path)) {
            return;
        }

        $_SESSION['latestVisitPath'] = $path;

        $data = Client::getIp() . ',' . date('Y-m-d H:i:s') . ',' . $path . ',' . $package . ',' . http_response_code() . ',' . $isAdmin;

        // If we don't have file perms, we ignore temp file writing
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

        $sql = 'INSERT INTO cmw_visits (visits_ip, visits_date, visits_path, visits_package, visits_code, visits_is_admin) VALUES ';
        foreach ($logs as $line) {
            $res = explode(',', $line);
            $sql .= "('$res[0]','$res[1]','$res[2]','$res[3]','$res[4]','$res[5]'),";
        }

        $db = DatabaseManager::getInstance();
        $db->query(mb_substr($sql, 0, -1));

        // Clean old file
        File::write($this->filePath, '');
    }

    private function getLogData(): array|false
    {
        return File::readArray($this->filePath);
    }

    public function getVisitsNumber(#[ExpectedValues(['all', 'monthly', 'week', 'day', 'hour'])] $period): ?int
    {
        if ($this->cachedVisits === null) {
            $db = DatabaseManager::getInstance();

            $queries = [
                'all' => "SELECT COUNT(DISTINCT visits_ip) AS result FROM cmw_visits",
                'day' => "SELECT COUNT(DISTINCT visits_ip) AS result FROM cmw_visits WHERE visits_date >= CURDATE()",
                'monthly' => "SELECT COUNT(DISTINCT visits_ip) AS result FROM cmw_visits WHERE visits_date >= DATE_FORMAT(NOW(), '%Y-%m-01')",
            ];

            foreach ($queries as $key => $sql) {
                $req = $db->prepare($sql);

                if (!$req->execute()) {
                    return 0;
                }

                $res = $req->fetch();

                if (!$res) {
                    return 0;
                }

                $this->cachedVisits[$key] = $res['result'] ?? 0;
            }
        }

        return ($this->cachedVisits[$period] ?? 0) + $this->getFileLineNumber();
    }


    /**
     * @param string $rangeStart
     * @param string $rangeFinish
     * @return int
     */
    public function getDataVisits(string $rangeStart, string $rangeFinish): int
    {
        $var = [
            'range_start' => $rangeStart,
            'range_finish' => $rangeFinish,
        ];

        $sql = 'SELECT COUNT(DISTINCT visits_ip) AS `result` FROM cmw_visits WHERE visits_date BETWEEN (:range_start) AND (:range_finish)';

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
        $sql = "SELECT DATE_FORMAT(visits_date, '%Y-%m') AS month, COUNT(*) AS visits
            FROM cmw_visits
            WHERE visits_date >= STR_TO_DATE(:date_limit, '%Y-%m-%d')
            GROUP BY month
            ORDER BY month;";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $dateLimit = date('Y-m-d', strtotime("-$pastMonths months")); // Pré-calcul de la date limite

        if (!$req->execute(['date_limit' => $dateLimit])) {
            return [];
        }

        return $req->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }


    /**
     * @param int $pastDays
     * @return array
     */
    public function getPastDaysVisits(int $pastDays): array
    {
        $sql = "SELECT DATE(visits_date) AS day, COUNT(*) AS visits
            FROM cmw_visits
            WHERE visits_date >= STR_TO_DATE(:date_limit, '%Y-%m-%d')
            GROUP BY day
            ORDER BY day;";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $dateLimit = date('Y-m-d', strtotime("-$pastDays days"));

        if (!$req->execute(['date_limit' => $dateLimit])) {
            return [];
        }

        return $req->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }


    /**
     * @param int $pastWeeks
     * @return array
     */
    public function getPastWeeksVisits(int $pastWeeks): array
    {
        $sql = "SELECT WEEK(visits_date, 1) AS week, COUNT(*) AS visits
            FROM cmw_visits
            WHERE visits_date >= STR_TO_DATE(:date_limit, '%Y-%m-%d')
            GROUP BY week
            ORDER BY week;";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        $dateLimit = date('Y-m-d', strtotime("-$pastWeeks weeks"));

        if (!$req->execute(['date_limit' => $dateLimit])) {
            return [];
        }

        return $req->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    /**
     * @return bool
     */
    private function checkPermissions(): bool
    {
        return PermissionManager::canCreateFile($this->dirStorage);
    }
}
