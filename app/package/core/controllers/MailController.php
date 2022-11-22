<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\MailModel;
use CMW\Router\Link;
use CMW\Utils\Response;
use CMW\Utils\Utils;
use CMW\Utils\View;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once(getenv("DIR") . 'app/package/core/vendors/PHPMailer/PHPMailer.php');
require_once(getenv("DIR") . 'app/package/core/vendors/PHPMailer/SMTP.php');
require_once(getenv("DIR") . 'app/package/core/vendors/PHPMailer/Exception.php');

/**
 * Class: @MailController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class MailController extends CoreController
{

    private MailModel $mailModel;

    public function __construct()
    {
        parent::__construct();
        $this->mailModel = new MailModel();
    }


    /**
     * @Param string $receiver -> mail to send
     * @Param string $subject -> subject of mail
     * @Param string $body -> html content with datas
     *
     */
    public function sendMailSMTP(string $receiver, string $subject, string $body): void
    {

        $config = $this->mailModel->getConfig();

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
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body . "<br>" . $config?->getFooter();

            //Send mail
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: $mail->ErrorInfo";
        }
    }

    /**
     * @param string $receiver
     * @param string $object
     * @param string $body
     * @return void
     * @desc Send mail with the default php function
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
        if($this->mailModel->getConfig() !== null && $this->mailModel->getConfig()->isEnable()){
            $this->sendMailSMTP($receiver, $object, $body);
        } else {
            $this->sendMailPHP($receiver, $object, $body);
        }

    }


    /////////// ADMIN DASHBOARD AREA \\\\\\\\\\\

    #[Link(path: "/mail", method: Link::GET, scope: "/cmw-admin")]
    #[Link("/configuration", Link::GET, [], "/cmw-admin/mail")]
    public function mailConfiguration(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.mail.configuration");

        $config = $this->mailModel->getConfig();

        View::createAdminView("core", "mailConfig")
            ->addScriptBefore("app/package/core/views/resources/js/mailConfig.js")
            ->addStyle("admin/resources/vendors/summernote/summernote-lite.css","admin/resources/assets/css/pages/summernote.css")
            ->addScriptAfter("admin/resources/vendors/jquery/jquery.min.js","admin/resources/vendors/summernote/summernote-lite.min.js","admin/resources/assets/js/pages/summernote.js")
            ->addVariableList(['config' => $config])
            ->view();
    }

    #[Link("/configuration", Link::POST, [], "/cmw-admin/mail")]
    public function mailConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.mail.configuration");

        [$mail, $mailReply, $addressSMTP, $user, $password, $port, $protocol, $footer, $enableSMTP] = Utils::filterInput(
            "mail", "mailReply", "addressSMTP", "user", "password", "port", "protocol", "footer", "enableSMTP");


        $this->mailModel->create($mail, $mailReply, $addressSMTP, $user, $password, $port, $protocol, $footer, (is_null($enableSMTP) ? 0 : 1));

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.config.success"));

        header("Location: configuration");
    }


    #[Link("/test", Link::POST, [], "/cmw-admin/mail", secure: true)]
    public function testMailConfigurationPost(): void
    {
        UsersController::redirectIfNotHavePermissions("core.dashboard", "core.mail.configuration");

        $receiver = filter_input(INPUT_POST,"receiver");

        $this->sendMail($receiver, "Test CraftMyWebsite - MAILS", "<p>Hello World !</p>");

        Response::sendAlert("success", LangManager::translate("core.toaster.success"),
            LangManager::translate("core.toaster.mail.test", ["%mail%" => $receiver]));

        header("Location: configuration");
    }

}