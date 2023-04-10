<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\ConditionModel;
use CMW\Model\users\UsersModel;
use CMW\Router\Link;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Manager\Views\View;

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
            ->addStyle("admin/resources/vendors/summernote/summernote-lite.css",
                "admin/resources/assets/css/pages/summernote.css")
            ->addScriptAfter("admin/resources/vendors/jquery/jquery.min.js",
                "admin/resources/vendors/summernote/summernote-lite.min.js",
                "admin/resources/assets/js/pages/summernote.js")
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

        //Include the public view file ("public/themes/$themePath/views/core/cgv.view.php")
        $view = new View('core', 'cgv');
        $view->addVariableList(["cgv" => $cgv]);
        $view->view();
    }


    #[Link('/cgu', Link::GET)]
    public function frontCGVPublic(): void
    {
        $cgu = $this->conditionModel->getCGU();

        //Include the public view file ("public/themes/$themePath/views/core/cgu.view.php")
        $view = new View('core', 'cgu');
        $view->addVariableList(["cgu" => $cgu]);
        $view->view();
    }
}