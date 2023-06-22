<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\MaintenanceModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;
use JetBrains\PhpStorm\NoReturn;


/**
 * Class: @MaintenanceController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class MaintenanceController extends AbstractController
{

    // Admin
    #[Link(path: "/manage", method: Link::GET, scope: "/cmw-admin/maintenance")]
    private function adminConfiguration(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.maintenance");

        $maintenance = MaintenanceModel::getInstance()->getMaintenance();

        View::createAdminView("Core", "maintenance")
            ->addVariableList(['maintenance' => $maintenance])
            ->view();
    }

    /**
     * @throws \Exception
     */
    #[NoReturn] #[Link(path: "/manage", method: Link::POST, scope: "/cmw-admin/maintenance")]
    private function adminConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.maintenance");

        [$title, $description, $targetDate, $type, $overrideThemeCode] = Utils::filterInput('title', 'description', 'target-date', 'type', 'overrideThemeCode');

        $isEnable = isset($_POST['isEnable']) ? 1 : 0;
        $isOverrideTheme = isset($_POST['isOverrideTheme']) ? 1 : 0;

        $targetDate = date('Y-m-d H:i:s', strtotime($targetDate));

        $updateMaintenance = MaintenanceModel::getInstance()->updateMaintenance($isEnable, $title, $description,
            $type, $targetDate, $isOverrideTheme, $overrideThemeCode);

        if ($isEnable === 1 && $updateMaintenance) {
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate('core.maintenance.settings.toaster.enabled'));
        } else if ($isEnable === 0 && $updateMaintenance) {
            Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
                LangManager::translate('core.maintenance.settings.toaster.disabled'));
        } else {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.maintenance.settings.toaster.error'));
        }

        Redirect::redirectPreviousRoute();
    }


    public function redirectMaintenance(): void
    {

        //Prevent loop
        if (Website::isCurrentPage('/maintenance')) {
            return;
        }

        //Ignore installer
        if (Website::isContainingRoute('installer')) {
            return;
        }

        $maintenance = MaintenanceModel::getInstance()->getMaintenance();
        $isEnable = $maintenance->isEnable();

        if (!$isEnable) {
            return;
        }

        //Check date
        if (time() >= strtotime($maintenance->getTargetDate())) {
            MaintenanceModel::getInstance()->updateMaintenance(0,
                $maintenance->getTitle(), $maintenance->getDescription(),
                $maintenance->getType(), $maintenance->getTargetDate(), $maintenance->isOverrideTheme(),
                $maintenance->getOverrideThemeCode());
            $this->redirectMaintenance();
        }

        $userCanByPass = UsersController::isAdminLogged() && UsersController::hasPermission('core.maintenance.bypass');

        if ($userCanByPass) {
            return;
        }

        ///// Login checks
        if ($maintenance->getType() === 0 &&
            (Website::isCurrentPage('/login') || Website::isCurrentPage('/register'))) {
            Redirect::redirect('maintenance');
        }

        if ($maintenance->getType() === 1 &&
            (Website::isCurrentPage('/login') || Website::isCurrentPage('/register'))) {
            return;
        }

        if ($maintenance->getType() === 2 &&
            (Website::isCurrentPage('/login'))) {
            return;
        }

        //Force redirect to maintenance page
        Redirect::redirect('maintenance');
    }

    // Public
    #[Link(path: "/maintenance", method: Link::GET, scope: "/")]
    private function publicMaintenance(): void
    {
        $maintenance = MaintenanceModel::getInstance()->getMaintenance();

        if (!$maintenance->isEnable()) {
            Redirect::redirectToHome();
        }

        if ($maintenance->isOverrideTheme()) {

            eval('?>' . $maintenance->getOverrideThemeCode());

        } else {
            $view = new View('Core', 'maintenance');
            $view->addVariableList(['maintenance' => $maintenance]);
            $view->view();
        }
    }

}
