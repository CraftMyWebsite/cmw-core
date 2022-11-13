<?php

namespace CMW\Manager\Error;

use CMW\Controller\Core\ThemeController;
use CMW\Manager\Permission\PermissionManager;
use CMW\Utils\Utils;
use DateTime;
use ErrorException;
use Throwable;

class ErrorManager
{

    private string $dirStorage = "app/storage/logs/";

    public function __invoke(): void
    {
        self::enableErrorDisplays();
        $this->handleError();
        $this->invokeCheckPermissions();
    }

    private function invokeCheckPermissions(): void
    {
        if (!$this->checkPermissions()) {
            echo <<<HTML
            <div class="cmw--errors">
                <h2>[MISSING PERMISSIONS]<br> Cannot create log file !</h2>
               <h3>It seems that it is impossible to create a log file in the path: <b>$this->dirStorage</b></h3>
            </div>
        HTML;
        }
    }

    private function checkPermissions(): bool
    {
        return PermissionManager::canCreateFile(Utils::getEnv()->getValue("DIR") . $this->dirStorage);
    }

    public static function enableErrorDisplays(): void
    {
        $devMode = (int)(Utils::getEnv()->getValue("devMode") ?? 0);
        ini_set('display_errors', $devMode);
        ini_set('display_startup_errors', $devMode);
        error_reporting(E_ALL);
    }

    public static function disableErrorDisplays(): void
    {
        ini_set('display_errors', 0);
        ini_set('display_startup_errors', 0);
        error_reporting(0);
    }

    private function handleError(): void
    {

        register_shutdown_function(
            function () {
                $this->checkForFatal();
            }
        );
        set_error_handler(
            function ($num, $str, $file, $line) {
                $this->logError($num, $str, $file, $line);
            }
        );
        set_exception_handler(
            function ($e) {
                $this->logException($e);
            }
        );

    }

    private function logError($num, $str, $file, $line): void
    {
        throw new ErrorException($str, 0, $num, $file, $line);
    }

    private function logException(Throwable $e): void
    {
        if ($this->checkPermissions()) {
            $message = $this->getLogMessage($e);
            file_put_contents("$this->dirStorage/{$this->getFileLogName()}", $message . PHP_EOL, FILE_APPEND);
        }

        if ((int)ini_get("display_errors") > 0) {
            echo $this->displayError($e);
        }
    }

    private function getFileLogName(): string
    {
        return "log_" . (new DateTime())->format("d-m-Y") . ".txt";
    }

    private function getLogMessage(Throwable $e): string
    {
        $date = (new DateTime())->format("H:i:s");
        $classType = get_class($e);
        return <<<EOL
        ==> CRAFTMYWEBSITE   : LOGGER SYSTEM
            [$date] Type     : $classType
            [$date] Message  : {$e->getMessage()}
            [$date] Location : {$e->getFile()}:{$e->getLine()}
            
        EOL;
    }

    private function displayError(Throwable $e): string
    {
        $classType = get_class($e);
        $trace = preg_replace("/#(\d)/", "<b>#$1</b><br>", $e->getTraceAsString());
        $trace = preg_replace("/<br>/", "</code><code style='margin: .6rem 0; display: block'>", $trace);
        return <<<HTML
            <div class="cmw--errors">
                <h2>[Internal Exception] Oops...</h2>
                <ul>
                    <li><b>Error Type:</b> <code>$classType</code></li>
                    <li><b>Error Message:</b> <code>{$e->getMessage()}</code></li>
                    <li><b>Location:</b> <code class="cmw--errors--location">{$e->getFile()}:{$e->getLine()}</code></li>
                </ul>
                <p>
                    <u>Trace :</u> <code class="cmw--errors--trace">{$trace}</code>
                </p>
                
                <small>This error has been saved in {$this->dirStorage}/{$this->getFileLogName()}</small>
            </div>
        HTML;

    }

    private function checkForFatal(): void
    {
        $error = error_get_last();
        if (!is_null($error) && $error["type"] === E_ERROR) {
            $this->logError($error["type"], $error["message"], $error["file"], $error["line"]);
        }
    }

    public static function showError(int $errorCode): void
    {
        http_response_code($errorCode);

        $pathUrl = Utils::getEnv()->getValue("PATH_URL");

        //Here, we get data page we don't want to redirect user, just show him an error.
        //Route /error get error file : $errorCode.view.php, if that file don't exist, we call default.view.php (from errors package)

        $currentTheme = ThemeController::getCurrentTheme()->getName();
        $defaultErrorFile = Utils::getEnv()->getValue("DIR") . "public/themes/$currentTheme/views/errors/default.view.php";

        if(!file_exists($defaultErrorFile)){
            self::getFallBackErrorPage($currentTheme);
            return;
        }

        $data = file_get_contents($pathUrl . "geterror/$errorCode");

        if (!$data) {
            echo "Error $errorCode.";
            return;
        }

        $data = str_replace("{errorCode}", $errorCode, $data);
        echo $data;

    }

    private static function getFallBackErrorPage(string $currentTheme): void
    {

        echo <<<HTML
                    <h1>Error, missing file !</h1>
                    <div class="container">
                        File missing : <pre>public/themes/$currentTheme/views/errors/default.view.php</pre>
                    </div>
                    HTML;
    }

}
