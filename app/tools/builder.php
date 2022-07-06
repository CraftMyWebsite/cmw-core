<?php

use CMW\Controller\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Model\Users\UsersModel;

function view(string $module, string $view, ?array $data, string $type, ?string $noAdminControl = null): void
{
    $toaster = bigToaster();

    if($type === 'admin' && !isset($data["userAdmin"]) && isset($_SESSION["cmwUserId"])) {

        $data["userAdmin"] = (new UsersModel())->getUserById($_SESSION["cmwUserId"]);
        $data["coreAdmin"] = new CoreController();
    }

    extract($data, EXTR_OVERWRITE);

    if ($type === 'admin') {

        if (is_null($noAdminControl)) {
            UsersController::isAdminLogged();
        }

        $path = "app/package/$module/views/$view.view.php";
        require_once($path);
        require_once(getenv("PATH_ADMIN_VIEW") . 'template.php');
    } else {
        $coreController = new CoreController();
        $theme = $coreController->cmwThemePath();

        $path = "public/themes/$theme/views/$module/$view.view.php";
        /* ob_start(); */
        require_once($path);
        /* $content = ob_get_clean(); */
        require_once("public/themes/$theme/views/template.php");
    }
}
