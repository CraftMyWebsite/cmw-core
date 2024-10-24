<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Mail\MailManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\MailModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @MailController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 0.0.1
 */
class MailController extends AbstractController
{

    #[Link(path: '/mail', method: Link::GET, scope: '/cmw-admin')]
    #[Link('/configuration', Link::GET, [], '/cmw-admin/mail')]
    private function mailConfiguration(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.settings.mails');

        $config = MailModel::getInstance()->getConfig();

        View::createAdminView('Core', 'Mail/mailConfig')
            ->addStyle('Admin/Resources/Vendors/Izitoast/iziToast.min.css')
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
                'Admin/Resources/Vendors/Tinymce/Config/full.js')
            ->addScriptAfter('App/Package/Core/Views/Mail/Resources/sendMail.js',
                'Admin/Resources/Vendors/Izitoast/iziToast.min.js')
            ->addVariableList(['config' => $config])
            ->view();
    }

    #[NoReturn] #[Link('/configuration', Link::POST, [], '/cmw-admin/mail')]
    private function mailConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.settings.mails');

        [$mail, $mailReply, $addressSMTP, $user, $password, $port, $protocol, $footer, $enableSMTP] = Utils::filterInput(
            'mail', 'mailReply', 'addressSMTP', 'user', 'password', 'port', 'protocol', 'footer', 'enableSMTP'
        );

        MailModel::getInstance()->create($mail,
            $mailReply,
            $addressSMTP,
            $user,
            $password,
            $port,
            $protocol,
            $footer,
            (is_null($enableSMTP) ? 0 : 1),
        );

        Flash::send(
            Alert::SUCCESS,
            LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.config.success'),
        );

        Redirect::redirectPreviousRoute();
    }

    #[Link('/test', Link::POST, [], '/cmw-admin/mail', secure: false)]
    private function testMailConfigurationPost(): void
    {
        if (!UsersController::hasPermission('core.dashboard', 'core.settings.mails')) {
            return;
        }

        $receiver = FilterManager::filterInputStringPost('receiver');
        MailManager::getInstance()->sendMail($receiver, 'Test CraftMyWebsite - MAILS', '<p>Hello World !</p>');
    }
}
