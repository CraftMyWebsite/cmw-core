<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Notification\NotificationModel;
use CMW\Manager\Package\AbstractController;

use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @NotificationController
 * @package CORE
 * @author Zomb
 * @version 0.0.1
 */
class NotificationController extends AbstractController
{
    #[NoReturn] #[Link("/notification/read/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin")]
    private function readNotification(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.notification.read");

        NotificationModel::getInstance()->readNotification($id);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/notification/unRead/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin")]
    private function unReadNotification(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.notification.read");

        NotificationModel::getInstance()->unReadNotification($id);

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/notification/readSelected", Link::POST, [], "/cmw-admin", secure: false)]
    private function adminReadSelectedPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.notification.read");

        $notificationIds = $_POST['selectedIds'];

        if (empty($notificationIds)) {
            Flash::send(Alert::ERROR, "Notifications", "Aucune notification(s) sélectionnée(s)");
            Redirect::redirectPreviousRoute();
        }

        foreach ($notificationIds as $notificationId) {
            $notificationId = FilterManager::filterData($notificationId, 11, FILTER_SANITIZE_NUMBER_INT);
            NotificationModel::getInstance()->readNotification($notificationId);
        }
        Flash::send(Alert::SUCCESS, "Notifications", "Séléction marqué comme lue");

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn] #[Link("/notification/goTo/:id", Link::GET, ["id" => "[0-9]+"], "/cmw-admin")]
    private function goToNotification(int $id): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.notification.read");

        $notification = NotificationModel::getInstance()->readNotification($id);

        Redirect::redirect(EnvManager::getInstance()->getValue('PATH_SUBFOLDER')."cmw-admin/".$notification->getSlug());
    }

    #[Link("/notifications", Link::GET, [], "/cmw-admin")]
    private function adminNotification(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.notification");

        $notifications = NotificationModel::getInstance()->getAllNotification();
        $packages = PackageController::getInstalledPackages();
        $showOnDiscord = NotificationModel::getInstance()->getSettingValue("showOnDiscord");
        $webhookDiscord = NotificationModel::getInstance()->getSettingValue("webhookDiscord");
        $sendMail = NotificationModel::getInstance()->getSettingValue("sendMail");
        $mailReceiver = NotificationModel::getInstance()->getSettingValue("mailReceiver");
        $refusedPackages = NotificationModel::getInstance()->getRefusedPackages();

        View::createAdminView('Core', 'Notification/main')
            ->addStyle("Admin/Resources/Assets/Css/simple-datatables.css")
            ->addScriptAfter("Admin/Resources/Vendors/Simple-datatables/simple-datatables.js",
                "Admin/Resources/Vendors/Simple-datatables/config-datatables.js")
            ->addVariableList(["notifications" => $notifications, "packages" => $packages, "showOnDiscord" => $showOnDiscord, "webhookDiscord" => $webhookDiscord, "sendMail" => $sendMail, "mailReceiver" => $mailReceiver, "refusedPackages" => $refusedPackages])
            ->view();
    }

    #[Link("/notifications", Link::POST, [], "/cmw-admin")]
    private function adminPostSettingsNotification(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.notification.settings");

        [$showOnDiscord, $webhookDiscord, $sendMail, $mailReceiver] = Utils::filterInput("show_on_discord", "discord_webhook_url", "send_mail", "mail_receiver");

        NotificationModel::getInstance()->updateSetting("showOnDiscord", $showOnDiscord ?? "");
        NotificationModel::getInstance()->updateSetting("webhookDiscord", $webhookDiscord ?? "");
        NotificationModel::getInstance()->updateSetting("sendMail", $sendMail ?? "");
        NotificationModel::getInstance()->updateSetting("mailReceiver", $mailReceiver ?? "");

        if (!empty($_POST['refused_package'])) {
            NotificationModel::getInstance()->clearRefusedPackage();
            foreach ($_POST['refused_package'] as $packageName) {
                NotificationModel::getInstance()->addRefusedPackage($packageName);
            }
        } else {
            NotificationModel::getInstance()->clearRefusedPackage();
        }

        Flash::send(Alert::SUCCESS, "Notifications", "Paramètres appliqué");

        Redirect::redirectPreviousRoute();
    }
}