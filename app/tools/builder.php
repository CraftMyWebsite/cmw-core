<?php

use CMW\Controller\coreController;
use CMW\Controller\Users\usersController;
use CMW\Model\Users\usersModel;

function view(string $module, string $view, ?array $data, string $type, ?string $noAdminControl = null): void
{
    $toaster = bigToaster();

    if($type === 'admin' && !isset($data["userAdmin"])) {
        $data["userAdmin"] = new usersModel();
        $data["coreAdmin"] = new coreController();
    }

    extract($data, EXTR_OVERWRITE);

    if ($type === 'admin') {

        if (is_null($noAdminControl)) {
            usersController::isAdminLogged();
        }

        $path = "app/package/$module/views/$view.view.php";
        require_once($path);
        require_once(getenv("PATH_ADMIN_VIEW") . 'template.php');
    } else {
        $coreController = new coreController();
        $theme = $coreController->cmwThemePath();

        $path = "public/themes/$theme/views/$module/$view.view.php";
        require_once($path);
        require_once("public/themes/$theme/views/template.php");
    }
}
