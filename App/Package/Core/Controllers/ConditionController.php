<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\ConditionModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @ConditionController
 * @package Condition
 * @author Zomb
 * @version 0.0.1
 */
class ConditionController extends AbstractController
{
    #[Link('/condition', Link::GET, [], '/cmw-admin')]
    private function conditionDashboard(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.settings.conditions');

        $cgv = ConditionModel::getInstance()->getCGV();
        $cgu = ConditionModel::getInstance()->getCGU();

        View::createAdminView('Core', 'Condition/condition')
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js', 'Admin/Resources/Vendors/Tinymce/Config/full.js')
            ->addVariableList(['cgv' => $cgv, 'cgu' => $cgu])
            ->view();
    }

    #[NoReturn] #[Link('/condition', Link::POST, [], '/cmw-admin')]
    private function conditionDashboardPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.settings.conditions');

        [$cguContent, $cguState, $cgvContent, $cgvState] = Utils::filterInput(
            'cguContent', 'cguState', 'cgvContent', 'cgvState'
        );

        $user = UsersSessionsController::getInstance()->getCurrentUser();

        if (is_null($user)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'));

            Redirect::redirectPreviousRoute();
        }

        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();

        $cguState = $cguState === NULL ? 0 : 1;
        $cgvState = $cgvState === NULL ? 0 : 1;

        ConditionModel::getInstance()->updateCondition($cguContent, $cguState, $cgvContent, $cgvState, $userId);

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.config.success'));

        Redirect::redirectPreviousRoute();
    }

    /* //////////////////// FRONT PUBLIC //////////////////// */

    #[Link('/cgv', Link::GET)]
    private function frontCGUPublic(): void
    {
        $cgv = ConditionModel::getInstance()->getCGV();

        if (!$cgv?->isState()) {
            Redirect::redirectToHome();
        }

        View::createPublicView('Core', 'cgv')->addVariableList(['cgv' => $cgv])->view();
    }

    #[Link('/cgu', Link::GET)]
    private function frontCGVPublic(): void
    {
        $cgu = ConditionModel::getInstance()->getCGU();

        if (!$cgu?->isState()) {
            Redirect::redirectToHome();
        }

        View::createPublicView('Core', 'cgu')->addVariableList(['cgu' => $cgu])->view();
    }
}
