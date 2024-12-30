<?php

namespace CMW\Manager\Mail;

use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Manager\AbstractManager;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\MailModel;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use function error_log;
use function mail;

require_once(EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Mail/Vendors/Phpmailer/PHPMailer.php');
require_once(EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Mail/Vendors/Phpmailer/SMTP.php');
require_once(EnvManager::getInstance()->getValue('DIR') . 'App/Manager/Mail/Vendors/Phpmailer/Exception.php');


class MailManager extends AbstractManager
{
    /**
     * @Param string $receiver -> mail to send
     * @Param string $subject -> subject of mail
     * @Param string $body -> html content with data
     */
    public function sendMailSMTP(string $receiver, string $subject, string $body, ?string $senderMail, ?string $senderName): bool
    {
        $config = MailModel::getInstance()->getConfig();

        if ($senderMail !== '') {
            $sender = $senderMail;
        } else {
            $sender = $config?->getMail();
        }
        if ($senderName !== '') {
            $name = $senderName;
        } else {
            $name = (new CoreModel())->fetchOption('name');
        }

        $mail = new PHPMailer(false);

        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;  // To enable verbose debug output → SMTP::DEBUG_SERVER;
            $mail->isSMTP();  // Send using SMTP
            $mail->Host = $config?->getAddressSMTP();  // Set the SMTP server to send through
            $mail->SMTPAuth = true;  // Enable SMTP authentication
            $mail->Username = $config?->getUser();  // SMTP username
            $mail->Password = $config?->getPassword();  // SMTP password
            $mail->SMTPSecure = $config?->getProtocol();  // TLS OR SSL
            $mail->Port = $config?->getPort();  // TCP port
            $mail->CharSet = 'UTF-8';

            // Receiver config
            $mail->setFrom($sender, $name);
            $mail->addAddress($receiver);
            $mail->addReplyTo($config?->getMailReply());

            // Content
            $mail->isHTML();
            $mail->Subject = $subject;
            $mail->Body = $body . '<br>' . $config?->getFooter();

            // Send mail
            $status = $mail->send();

            if (!$status) {
                error_log("Message could not be sent. Mailer Error: $mail->ErrorInfo");
            }

            return $status;
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: $e";
            error_log("Message could not be sent. Mailer Error: $e");
            return false;
        }
    }

    /**
     * @param string $receiver
     * @param string $object
     * @param string $body
     * @return bool
     * @desc Send mail with the Default php function
     */
    private function sendMailPHP(string $receiver, string $object, string $body): bool
    {
        return mail($receiver, $object, $body);
    }

    /**
     * @param string $receiver
     * @param string $object
     * @param string $body
     * @return bool
     * @desc Send mail (SMTP OR PHP MAIL function)
     */
    public function sendMail(string $receiver, string $object, string $body): bool
    {
        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            return $this->sendMailSMTP($receiver, $object, $body, '', '');
        }

        return $this->sendMailPHP($receiver, $object, $body);
    }

    /**
     * @param string $senderMail
     * @param string $senderName
     * @param string $receiver
     * @param string $object
     * @param string $body
     * @return void
     * @desc Send mail (SMTP ONLY)
     */
    public function sendMailWithSender(string $senderMail, string $senderName, string $receiver, string $object, string $body): void
    {
        if (MailModel::getInstance()->getConfig() !== null && MailModel::getInstance()->getConfig()->isEnable()) {
            $this->sendMailSMTP($receiver, $object, $body, $senderMail, $senderName);
        } else {
            Flash::send(Alert::ERROR, 'Erreur', 'Il y à un problème dans votre configuration SMTP !');
        }
    }
}
