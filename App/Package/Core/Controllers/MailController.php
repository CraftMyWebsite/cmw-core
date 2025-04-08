<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Interface\Core\IMailTemplate;
use CMW\Interface\Shop\IVirtualItems;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Mail\MailManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\MailModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;
use function http_response_code;
use function is_null;

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
        $mailTemplates =  $this->getMailTemplateMethods();

        usort($mailTemplates, function ($a, $b) {
            $priority = ['empty', 'empty_signed'];
            $aPriority = in_array($a->getVarName(), $priority) ? array_search($a->getVarName(), $priority) : PHP_INT_MAX;
            $bPriority = in_array($b->getVarName(), $priority) ? array_search($b->getVarName(), $priority) : PHP_INT_MAX;

            return $aPriority <=> $bPriority;
        });

        View::createAdminView('Core', 'Mail/mailConfig')
            ->addStyle('Admin/Resources/Vendors/Izitoast/iziToast.min.css')
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js',
                'App/Package/Core/Views/Mail/Resources/tinyMCEConfig.js')
            ->addScriptAfter('App/Package/Core/Views/Mail/Resources/sendMail.js',
                'Admin/Resources/Vendors/Izitoast/iziToast.min.js')
            ->addVariableList(['config' => $config, 'mailTemplates' => $mailTemplates])
            ->view();
    }

    #[NoReturn] #[Link('/configuration', Link::POST, [], '/cmw-admin/mail')]
    private function mailConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.settings.mails');

        [$mail, $mailReply, $addressSMTP, $user, $password, $port, $protocol, $body, $enableSMTP] = Utils::filterInput(
            'mail', 'mailReply', 'addressSMTP', 'user', 'password', 'port', 'protocol', 'body', 'enableSMTP'
        );

        if (!str_contains($body, '[MAIL_CONTENT]')) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'), LangManager::translate('core.mail.editor.render_alert'));
            Redirect::redirectPreviousRoute();
        }

        MailModel::getInstance()->create($mail,
            $mailReply,
            $addressSMTP,
            $user,
            $password,
            $port,
            $protocol,
            $body,
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
        $status = MailManager::getInstance()->sendMail($receiver, 'Test CraftMyWebsite - MAILS', '<p>Hello World !</p>');

        if ($status) {
            http_response_code(204);
        } else {
            http_response_code(500);
        }
    }

    /**
     * @return IMailTemplate[]
     */
    public function getMailTemplateMethods(): array
    {
        return Loader::loadImplementations(IMailTemplate::class);
    }
}
