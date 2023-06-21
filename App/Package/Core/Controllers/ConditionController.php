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
use CMW\Utils\Redirect;
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
            ->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js","Admin/Resources/Vendors/Tinymce/Config/full.js")
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

        Redirect::redirectPreviousRoute();
    }

    /* //////////////////// FRONT PUBLIC //////////////////// */

    #[Link('/cgv', Link::GET)]
    private function frontCGUPublic(): void
    {
        $cgv = ConditionModel::getInstance()->getCGV();

        //Include the Public view file ("Public/Themes/$themePath/Views/Core/cgv.view.php")
        $view = new View('Core', 'cgv');
        $view->addVariableList(["cgv" => $cgv]);
        $view->view();
    }


    #[Link('/cgu', Link::GET)]
    private function frontCGVPublic(): void
    {
        $cgu = ConditionModel::getInstance()->getCGU();

        //Include the Public view file ("Public/Themes/$themePath/Views/Core/cgu.view.php")
        $view = new View('Core', 'cgu');
        $view->addVariableList(["cgu" => $cgu]);
        $view->view();
    }
}