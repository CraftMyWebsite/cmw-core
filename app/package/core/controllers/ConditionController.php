<?php

namespace CMW\Controller\Core;

use CMW\Model\Core\ConditionModel;

use CMW\Controller\Core\CoreController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\CoreModel;
use CMW\Router\Link;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Utils\View;
use CMW\Controller\Users\UsersController;
use CMW\Model\users\UsersModel;
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
        UsersController::redirectIfNotHavePermissions("core.dashboard");

        $cgv = $this->conditionModel->getCGV();
        $cgu = $this->conditionModel->getCGU();
        
        View::createAdminView("core", "condition")
        ->addStyle("admin/resources/vendors/summernote/summernote-lite.css","admin/resources/assets/css/pages/summernote.css")
        ->addScriptAfter("admin/resources/vendors/jquery/jquery.min.js","admin/resources/vendors/summernote/summernote-lite.min.js","admin/resources/assets/js/pages/summernote.js")
        ->addVariableList(["cgv" => $cgv,"cgu" => $cgu])
        ->view();
    }

    #[Link("/condition", Link::POST, [], "/cmw-admin")]
    public function conditionDashboardPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard");

        [$conditionId, $conditionContent, $conditionState] = Utils::filterInput("conditionId", "conditionContent", "conditionState");

        //Get the current author
        $user = new UsersModel;
        $userEntity = $user->getUserById($_SESSION['cmwUserId']);
        $conditionAuthor = $userEntity?->getId();

        $this->conditionModel->updateCondition($conditionId, $conditionContent, $conditionState === NULL ? 0 : 1, $conditionAuthor);

        Response::sendAlert("success", LangManager::translate("core.toaster.success"), LangManager::translate("core.toaster.config.success"));

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