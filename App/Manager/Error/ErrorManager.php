<?php

namespace CMW\Manager\Error;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Permission\PermissionManager;
use CMW\Manager\Theme\ThemeManager;
use JetBrains\PhpStorm\NoReturn;
use DateTime;
use ErrorException;
use Throwable;

class ErrorManager
{
    private string $dirStorage = 'App/Storage/Logs';

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
        return PermissionManager::canCreateFile(EnvManager::getInstance()->getValue('DIR') . $this->dirStorage);
    }

    /**
     * @param bool $force
     * @return void
     * @desc $force is for forced the errorDisplay when you are not in dev mode
     */
    public static function enableErrorDisplays(bool $force = false): void
    {
        $devMode = (int) (EnvManager::getInstance()->getValue('devMode') ?? 0);

        if ($force) {
            $devMode = 1;
        }

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

        if ((int) ini_get('display_errors') > 0) {
            echo $this->displayError($e);
        }
    }

    private function getFileLogName(): string
    {
        return 'log_' . (new DateTime())->format('d-m-Y') . '.txt';
    }

    private function getLogMessage(Throwable $e): string
    {
        $date = (new DateTime())->format('H:i:s');
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
        $trace = preg_replace('/#(\d)/', '<b>#$1</b><br>', $e->getTraceAsString());
        $trace = preg_replace('/<br>/', "</code><code style='margin: .6rem 0; display: block'>", $trace);
        return <<<HTML
            <style>
                .error {
                    background: #343749;
                    font-family: Verdana, sans-serif;
                    color: white;
                    padding: 1rem;
                }
                h2 {
                    text-align: center;
                }
                h4 {
                    text-align: center;
                }
                .error-message {
                    color: red;
                    font-size: 1rem;
                }
                .file {
                    color: yellowgreen;
                    font-size: 1rem;
                }
                .line {
                    color: #ABB015;
                    font-weight: bold;
                    font-size: 1rem;
                }
                a {
                    color: red;
                }
            </style>
                <div class="error">
                    <h2>$classType</h2>
                        <p>Error : <code class="error-message">{$e->getMessage()}</code></p>
                        <p>Found in <code><span class="file">{$e->getFile()}</span></code> at the line <span class="line">{$e->getLine()}</span><p>
                    <p>Trace : <code class="trace">$trace</code></p>
                    
                    <small>This error has been saved in $this->dirStorage/{$this->getFileLogName()}</small>
                    <h4>User, if you encounter this error please report it to the administrator !</h4>
                    <h4>Administrator, if you can't fix this error please refer to the <a href="">documentation</a> or contact CraftMyWebsite on the <a href="">Discord</a></h4>
                </div>
            HTML;
    }

    private function checkForFatal(): void
    {
        $error = error_get_last();
        if (!is_null($error) && $error['type'] === E_ERROR) {
            $this->logError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    public static function showError(int $errorCode): void
    {
        http_response_code($errorCode);

        $pathUrl = EnvManager::getInstance()->getValue('PATH_URL');

        // Here, we get data page we don't want to redirect user, just show him an error.
        // Route /error get error file : $errorCode.view.php, if that file don't exist, we call Default.view.php (from errors package)

        $currentTheme = ThemeManager::getInstance()->getCurrentTheme()->name();
        $defaultErrorFile = EnvManager::getInstance()->getValue('DIR') . "Public/Themes/$currentTheme/Views/Errors/default.view.php";
        $errorFile = EnvManager::getInstance()->getValue('DIR') . "Public/Themes/$currentTheme/Views/Errors/$errorCode.view.php";

        if (file_exists($errorFile)) {
            include $errorFile;
            return;
        }

        if (file_exists($defaultErrorFile)) {
            include $defaultErrorFile;
            return;
        }

        self::getFallBackErrorPage($currentTheme);
    }

    private static function getFallBackErrorPage(string $currentTheme): void
    {
        echo <<<HTML
            <h1>Error, missing file !</h1>
            <div class="container">
                Files missing : <pre>Public/Themes/$currentTheme/Views/Errors/default.view.php</pre>
            </div>
            HTML;
    }

    /**
     * @param string $title
     * @param string $content
     * @return void
     * @desc Display a custom error page.
     */
    #[NoReturn]
    public static function showCustomErrorPage(string $title, string $content): void
    {
        echo <<<HTML
                    <div>
                        <h1>$title</h1>
                        <p>$content</p>
                    </div>
            HTML;
        die();
    }

    /**
     * @param string $title
     * @param string $content
     * @return void
     * @desc Display a custom error page.
     */
    public static function showCustomWarning(string $title, string $content): void
    {
        echo <<<HTML
                        <div style="display: flex;
                                    flex-wrap: nowrap;
                                    flex-direction: row;
                                    justify-content: center;
                                    align-items: center;
                                    margin-top: 50px;">
                            <div class="alert-warning">
                                <b>$title</b>
                                <br>
                                $content
                            </div>
                        </div>
            HTML;
    }
}
