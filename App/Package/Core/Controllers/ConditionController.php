<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\ConditionModel;
use CMW\Utils\Response;
use CMW\Utils\Utils;

/**
 * Class: @ConditionController
 * @package Condition
 * @author Zomb
 * @version 1.0
 */
class ConditionController extends CoreController
{

    private ConditionModel $conditionModel;

    public function __construct()
    {
        parent::__construct();
        $this->conditionModel = new ConditionModel();
    }

    #[Link("/condition", Link::GET, [], "/cmw-admin")]
    public function conditionDashboard(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.condition.edit");

        $cgv = $this->conditionModel->getCGV();
        $cgu = $this->conditionModel->getCGU();

        View::createAdminView("core", "condition")
            ->addStyle("Admin/Resources/Vendors/Summernote/summernote-lite.css",
                "Admin/Resources/Assets/Css/Pages/summernote.css")
            ->addScriptAfter("Admin/Resources/Vendors/jquery/jquery.min.js",
                "Admin/Resources/Vendors/Summernote/summernote-lite.min.js",
                "Admin/Resources/Assets/Js/Pages/summernote.js")
            ->addVariableList(["cgv" => $cgv, "cgu" => $cgu])
            ->view();
    }

    #[Link("/condition", Link::POST, [], "/cmw-admin")]
    public function conditionDashboardPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.condition.edit");

        [$conditionId, $conditionContent, $conditionState] = Utils::filterInput("conditionId",
            "conditionContent", "conditionState");

        $this->conditionModel->updateCondition($conditionId, $conditionContent,
            $conditionState === NULL ? 0 : 1, $_SESSION['cmwUserId']);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("Location: condition");
    }

    /* //////////////////// FRONT PUBLIC //////////////////// */

    #[Link('/cgv', Link::GET)]
    public function frontCGUPublic(): void
    {
        $cgv = $this->conditionModel->getCGV();

        //Include the Public view file ("Public/Themes/$themePath/Views/Core/cgv.view.php")
        $view = new View('core', 'cgv');
        $view->addVariableList(["cgv" => $cgv]);
        $view->view();
    }


    #[Link('/cgu', Link::GET)]
    public function frontCGVPublic(): void
    {
        $cgu = $this->conditionModel->getCGU();

        //Include the Public view file ("Public/Themes/$themePath/Views/Core/cgu.view.php")
        $view = new View('core', 'cgu');
        $view->addVariableList(["cgu" => $cgu]);
        $view->view();
    }
}