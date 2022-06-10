<?php

global $router, $_UTILS;

use CMW\Controller\Installer\installerController;

$installationStep = $_UTILS::getEnv()->getValue("installStep") ?? 0;
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


$router->scope("/installer", function ($router) use ($number) {
    $capsMaj = ucfirst($number);
    $router->get("/", "Installer#{$number}InstallView");
    $router->post("/submit{$capsMaj}Install", "Installer#{$number}InstallPost");

    $router->get('/lang/:code', function($code) {
        (new installerController())->changeLang($code);
    })->with('slug', '.*?');
});