<?php

global $router;

use CMW\Controller\Installer\InstallerController;
use CMW\Router\Router;
use CMW\Utils\Utils;

$installationStep = Utils::getEnv()->getValue("installStep") ?? 0;
$number ??= "first";

switch ((int)$installationStep) {
    case 0:
        $number = "first";
        break;
    case 1:
        $number = "second";
        break;
    case 2:
        $number = "third";
        break;
    case 3:
        $number = "fourth";
        break;
    case 4:
        $number = "fifth";
        break;
}


$router->scope("/installer", function (Router $router) use ($number) {
    $capsMaj = ucfirst($number);

    $router->get('/lang/:code', function($code) {
        (new InstallerController())->changeLang($code);
    })->with('slug', '.*?');
});