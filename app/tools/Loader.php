<?php

namespace CMW\Utils;

use CMW\Controller\Installer\InstallerController;
use CMW\Router\Router;
use CMW\Router\RouterException;

class Loader
{

    private Utils $utils;
    private static Router $globalRouter;

    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
        $this->loadRouter();
    }

    private function getValue(string $value): string
    {
        return $this->utils::getEnv()->getValue($value);
    }

    public function manageErrors(): void
    {
        $devMode = (bool)$this->getValue("devMode");
        ini_set('display_errors', $devMode);
        ini_set('display_startup_errors', $devMode);
        error_reporting(E_ALL);
    }

    public function loadRouter($url = ""): Router
    {
        if (!isset(self::$globalRouter)) {
            require_once($this->getValue("dir") . "router/router.php");
            require_once($this->getValue("dir") . "router/route.php");
            require_once($this->getValue("dir") . "router/routerException.php");

            $router = new Router($_GET['url'] ?? $url);
            self::$globalRouter = $router;
        }

        return self::$globalRouter;
    }

    public function loadPackages(): void
    {
        require_once($this->getValue("dir") . "app/manager.php");
        require_once($this->getValue("dir") . "app/package-loader/__model.php");
        require_once($this->getValue("dir") . "app/package-loader/__controller.php");
        require_once($this->getValue("dir") . "app/package-loader/__entity.php");
        require_once($this->getValue("dir") . "app/package-loader/__routes.php");
        if ((int)$this->getValue("installStep") >= 0) {
            require_once($this->getValue("dir") . "installation/routes.php");
            require_once($this->getValue("dir") . "installation/controllers/installerController.php");
            require_once($this->getValue("dir") . "installation/models/installerModel.php");
        }
    }

    public function loadGlobalConstants(): void
    {
        require_once($this->getValue("dir") . "app/tools/builder.php");
        require_once($this->getValue("dir") . "app/tools/functions.php");
        require_once($this->getValue("dir") . "app/globalConst.php");
    }

    public function listenRouter(): void
    {
        $router = self::$globalRouter;

        try {
            $router->listen();
        } catch (RouterException $e) {
            exit($e->getMessage());
        }
    }

    public function installManager(): void
    {
        if (is_dir("installation")) {
            if ((int)$this->getValue("installStep") >= 0) {
                require_once($this->getValue("dir") . "installation/controllers/installerController.php");

                $installation = new InstallerController();

                $installation->goToInstall();
            } elseif (!$this->getValue("devMode")) {
                deleteDirectory("installation");
            }
        }
    }

    public function loadFunctions(): void
    {

        echo <<<HTML
        <script>
            const callPostFunction = (forumId, resToSend) => {
                const formRaw    = document.getElementById(forumId);

                formRaw.onsubmit = e => {
                    e.preventDefault();
            
                    const formData   = new FormData(formRaw);
                    const link = window.location.pathname;
                    fetch(link, {
                        method: "post",
                        body  : formData
                    }).then(v => v.text())
                        .then(res => {
                            if (+res > 0) {
                                console.log("ok")
                                //window.location.reload();
                            } else {
                                //Todo, Alert system
                            }
            
                        })
                }
            }
        </script>
        HTML;


    }

}