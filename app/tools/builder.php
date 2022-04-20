<?php

use CMW\Controller\coreController;
use CMW\Controller\Users\usersController;

function view(string $module, string $view, ?array $data, string $type, ?string $noAdminControl = null){
    $toaster = bigToaster();

    extract($data);

    if($type == 'admin'){
        if($noAdminControl == null) {
            usersController::isAdminLogged();
        }

        $path = "app/package/".$module."/views/".$view.".view.php";
        require_once($path);
        require_once(getenv("PATH_ADMIN_VIEW").'template.php');
    }
    else {
        $coreController = new coreController();
        $theme = $coreController->cmwThemePath();

        $path = "public/themes/".$theme."/views/".$module."/".$view.".view.php";
        require_once($path);
        require_once("public/themes/".$theme."/views/template.php");
    }
}
