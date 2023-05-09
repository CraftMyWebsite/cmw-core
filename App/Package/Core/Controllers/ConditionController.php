<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\ConditionModel;
use CMW\Utils\Utils;

/**
 * Class: @ConditionController
 * @package Condition
 * @author Zomb
 * @version 1.0
 */
class ConditionController extends AbstractController
{
    #[Link("/condition", Link::GET, [], "/cmw-admin")]
    private function conditionDashboard(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.condition.edit");

        $cgv = ConditionModel::getInstance()->getCGV();
        $cgu = ConditionModel::getInstance()->getCGU();

        View::createAdminView("Core", "condition")
            ->addStyle("Admin/Resources/Vendors/Summernote/summernote-lite.css",
                "Admin/Resources/Assets/Css/Pages/summernote.css")
            ->addScriptAfter("Admin/Resources/Vendors/jquery/jquery.min.js",
                "Admin/Resources/Vendors/Summernote/summernote-lite.min.js",
                "Admin/Resources/Assets/Js/Pages/summernote.js")
            ->addVariableList(["cgv" => $cgv, "cgu" => $cgu])
            ->view();
    }

    #[Link("/condition", Link::POST, [], "/cmw-admin")]
    private function conditionDashboardPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.condition.edit");

        [$conditionId, $conditionContent, $conditionState] = Utils::filterInput("conditionId",
            "conditionContent", "conditionState");

        ConditionModel::getInstance()->updateCondition($conditionId, $conditionContent,
            $conditionState === NULL ? 0 : 1, $_SESSION['cmwUserId']);

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("Location: condition"); //Todo redirect ?
    }

    /* //////////////////// FRONT PUBLIC //////////////////// */

    #[Link('/cgv', Link::GET)]
    private function frontCGUPublic(): void
    {
        $cgv = ConditionModel::getInstance()->getCGV();

        //Include the Public view file ("Public/Themes/$themePath/Views/Core/cgv.view.php")
        $view = new View('core', 'cgv');
        $view->addVariableList(["cgv" => $cgv]);
        $view->view();
    }


    #[Link('/cgu', Link::GET)]
    private function frontCGVPublic(): void
    {
        $cgu = ConditionModel::getInstance()->getCGU();

        //Include the Public view file ("Public/Themes/$themePath/Views/Core/cgu.view.php")
        $view = new View('core', 'cgu');
        $view->addVariableList(["cgu" => $cgu]);
        $view->view();
    }
}