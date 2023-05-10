<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\MailModel;
use CMW\Utils\Utils;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once(getenv("DIR") . 'App/Package/Core/Vendors/Phpmailer/PHPMailer.php');
require_once(getenv("DIR") . 'App/Package/Core/Vendors/Phpmailer/SMTP.php');
require_once(getenv("DIR") . 'App/Package/Core/Vendors/Phpmailer/Exception.php');

/**
 * Class: @MailController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class MailController extends AbstractController
{


    /**
     * @Param string $receiver -> mail to send
     * @Param string $subject -> subject of mail
     * @Param string $body -> html content with datas
     *
     */
    public function sendMailSMTP(string $receiver, string $subject, string $body): void
    {

        $config = MailModel::getInstance()->getConfig();

        $mail = new PHPMailer(false);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;                    //To enable verbose debug output → SMTP::DEBUG_SERVER;
            $mail->isSMTP();                                       //Send using SMTP
            $mail->Host = $config?->getAddressSMTP();               //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                //Enable SMTP authentication
            $mail->Username = $config?->getUser();                  //SMTP username
            $mail->Password = $config?->getPassword();              //SMTP password
            $mail->SMTPSecure = $config?->getProtocol();            //TLS OR SSL
            $mail->Port = $config?->getPort();                      //TCP port
            $mail->CharSet = 'UTF-8';

            //Receiver config
            $mail->setFrom($config?->getMail(), (new CoreModel())->fetchOption("name"));
            $mail->addAddress($receiver);
            $mail->addReplyTo($config?->getMailReply());

            //Content
            $mail->isHTML();
            $mail->Subject = $subject;
            $mail->Body = $body . "<br>" . $config?->getFooter();

            //Send mail
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: $e";
        }
    }

    /**
     * @param string $receiver
     * @param string $object
     * @param string $body
     * @return void
     * @desc Send mail with the Default php function
     */
    private function sendMailPHP(string $receiver, string $object, string $body): void
    {
        mail($receiver, $object, $body);
    }

    /**
     * @param string $receiver
     * @param string $object
     * @param string $body
     * @return void
     * @desc Send mail (SMTP OR PHP MAIL function)
     */
    public function sendMail(string $receiver, string $object, string $body): void
    {
        if(MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()){
            $this->sendMailSMTP($receiver, $object, $body);
        } else {
            $this->sendMailPHP($receiver, $object, $body);
        }

    }


    /////////// ADMIN DASHBOARD AREA \\\\\\\\\\\

    #[Link(path: "/mail", method: Link::GET, scope: "/cmw-admin")]
    #[Link("/configuration", Link::GET, [], "/cmw-admin/mail")]
    private function mailConfiguration(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.mail.configuration");

        $config = MailModel::getInstance()->getConfig();

        View::createAdminView("Core", "mailConfig")
            ->addScriptBefore("Admin/Resources/Vendors/Tinymce/tinymce.min.js")
            ->addVariableList(['config' => $config])
            ->view();
    }

    #[Link("/configuration", Link::POST, [], "/cmw-admin/mail")]
    private function mailConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.mail.configuration");

        [$mail, $mailReply, $addressSMTP, $user, $password, $port, $protocol, $footer, $enableSMTP] = Utils::filterInput(
            "mail", "mailReply", "addressSMTP", "user", "password", "port", "protocol", "footer", "enableSMTP");


        MailModel::getInstance()->create($mail, $mailReply, $addressSMTP, $user, $password, $port, $protocol, $footer, (is_null($enableSMTP) ? 0 : 1));

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("Location: configuration");
    }


    #[Link("/test", Link::POST, [], "/cmw-admin/mail", secure: true)]
    private function testMailConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.mail.configuration");

        $receiver = filter_input(INPUT_POST,"receiver");

        $this->sendMail($receiver, "Test CraftMyWebsite - MAILS", "<p>Hello World !</p>");

        Flash::send(Alert::SUCCESS, LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.mail.test", ["%mail%" => $receiver]));

        header("Location: configuration");
    }

}