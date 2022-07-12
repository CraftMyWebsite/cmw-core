<?php

use CMW\Controller\CoreController;
use CMW\Controller\Users\UsersController;
use CMW\Model\Users\UsersModel;


/**
 * Example includes
 *
 * $includes = array(
 *            "scripts" => [
 *                "before" => [
 *                    "link",
 *                    "link",
 *                    "link"
 *            ],
 *                "after" => [
 *                    "link",
 *                    "link",
 *                    "link"
 *                ]
 *            ],
 *            "styles" => [
 *                "link",
 *                "link",
 *                "link"
 *            ]
 *            );
 */

/**
 *
 * @param string $module
 * @param string $view
 * @param array|null $data
 * @param string $type
 * @param array|null $includes
 * @param string|null $noAdminControl
 * @return void
 */
function view(string $module, string $view, ?array $data, string $type, ?array $includes, ?string $noAdminControl = null): void
{
    $toaster = bigToaster();

    if ($type === 'admin' && !isset($data["userAdmin"]) && isset($_SESSION["cmwUserId"])) {

        $data["userAdmin"] = (new UsersModel())->getUserById($_SESSION["cmwUserId"]);
        $data["coreAdmin"] = new CoreController();
    }

    extract($data, EXTR_OVERWRITE);

    /* ADMIN */
    if ($type === 'admin') {

        if (is_null($noAdminControl)) {
            UsersController::redirectIfNotHavePermissions("core.dashboard");
        }

        $path = "app/package/$module/views/$view.view.php";
        ob_start();
        require_once($path);
        $content = ob_get_clean();
        require_once(getenv("PATH_ADMIN_VIEW") . 'template.php');


        /* PUBLIC */
    } else {
        $coreController = new CoreController();
        $theme = $coreController->cmwThemePath();


        $path = "public/themes/$theme/views/$module/$view.view.php";
        ob_start();
        require_once($path);
        $content = ob_get_clean();
        require_once("public/themes/$theme/views/template.php");
    }
}

function includeFiles(?array $includes, string... $types): void
{

    foreach ($types as $type) {

        switch ($type):

            case "scriptsBefore":
                if(!empty($includes['scripts']['before'])) {
                    foreach ($includes['scripts']['before'] as $value) {
                        echo '<script src="' . getenv("PATH_SUBFOLDER") . $value . '"></script>';
                    }
                }
                break;

            case "scriptsAfter":
                if(!empty($includes['scripts']['after'])) {
                    foreach ($includes['scripts']['after'] as $value) {
                        echo '<script src="' . getenv("PATH_SUBFOLDER") . $value . '"></script>';
                    }
                }
                break;

            case "styles":
                if(!empty($includes['styles'])) {
                    foreach ($includes['styles'] as $value) {
                        echo '<link rel="stylesheet" href="' . getenv("PATH_SUBFOLDER") . $value . '"></style>';
                    }
                }
                break;

        endswitch;
    }
}
