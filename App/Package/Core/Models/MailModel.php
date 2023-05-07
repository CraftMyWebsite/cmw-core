<?php

namespace CMW\Model\Core;

use CMW\Entity\Core\MailConfigEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Utils\EnvManager;

class MailModel extends DatabaseManager
{


    /**
     * @param string $mail
     * @param string $mailReply
     * @param string $addressSMTP
     * @param string $user
     * @param string $password
     * @param int $port
     * @param string $protocol
     * @param string $footer
     * @param int $enable
     * @return \CMW\Entity\Core\MailConfigEntity|null
     * @desc Create or edit a config (only 1 config is allowed)
     */
    public function create(string $mail, string $mailReply, string $addressSMTP, string $user, string $password, int $port, string $protocol, string $footer, int $enable): ?MailConfigEntity
    {
        if ($this->configExist() && !is_null($this->getConfig())) {
            $id = $this->getConfig()?->getId();

            return $this->update($id, $mail, $mailReply, $addressSMTP, $user, $password, $port, $protocol, $footer, $enable);
        }

        $var = array(
            "mail" => $mail,
            "mailReply" => $mailReply,
            "addressSMTP" => $addressSMTP,
            "user" => $user,
            "port" => $port,
            "protocol" => $protocol,
            "footer" => $footer,
            "enable" => $enable
        );

        $sql = "INSERT INTO cmw_mail_config_smtp (mail_config_mail, mail_config_mail_reply, mail_config_address_smtp, 
                            mail_config_user, mail_config_port, mail_config_protocol, mail_config_footer, mail_config_enable) 
                VALUES (:mail, :mailReply, :addressSMTP, :user, :port, :protocol, :footer, :enable)";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            //We store the password in the .env file
            EnvManager::getInstance()->setOrEditValue("SMTP_PASSWORD", $password);
            //We return the current config
            return $this->getConfig();
        }

        return null;
    }

    /**
     * @return bool
     * @desc Check if we already have a config
     */
    public function configExist(): bool
    {
        $sql = "SELECT * FROM `cmw_mail_config_smtp`";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute()) {
            return count($req->fetchAll());
        }

        return 0;
    }

    public function getConfig(): ?MailConfigEntity
    {
        $sql = "SELECT * FROM cmw_mail_config_smtp LIMIT 1 ";

        $db = self::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        return new MailConfigEntity(
            $res['mail_config_id'] ?? "",
            $res['mail_config_mail'] ?? "",
            $res['mail_config_mail_reply'] ?? "",
            $res['mail_config_address_smtp'] ?? "",
            $res['mail_config_user'] ?? "",
            EnvManager::getInstance()->getValue("SMTP_PASSWORD") ?? "",
            $res['mail_config_port'] ?? "",
            $res['mail_config_protocol'] ?? "",
            $res['mail_config_footer'] ?? "",
            $res['mail_config_enable'] ?? ""
        );
    }

    /**
     * @param int $id
     * @param string $mail
     * @param string $mailReply
     * @param string $addressSMTP
     * @param string $user
     * @param string $password
     * @param int $port
     * @param string $protocol
     * @param string $footer
     * @param int $enable
     * @return \CMW\Entity\Core\MailConfigEntity|null
     * @desc Update the current config
     */
    public function update(int $id, string $mail, string $mailReply, string $addressSMTP, string $user, string $password, int $port, string $protocol, string $footer, int $enable): ?MailConfigEntity
    {
        $var = array(
            "id" => $id,
            "mail" => $mail,
            "mailReply" => $mailReply,
            "addressSMTP" => $addressSMTP,
            "user" => $user,
            "port" => $port,
            "protocol" => $protocol,
            "footer" => $footer,
            "enable" => $enable
        );

        $sql = "UPDATE cmw_mail_config_smtp SET mail_config_mail = :mail, mail_config_mail_reply = :mailReply, 
                mail_config_address_smtp = :addressSMTP, mail_config_user = :user, mail_config_port = :port, 
                mail_config_protocol = :protocol, mail_config_footer = :footer, mail_config_enable = :enable
                WHERE mail_config_id = :id";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            //We store the password in the .env file
            EnvManager::getInstance()->setOrEditValue("SMTP_PASSWORD", $password);
            //We return the current config
            $this->getConfig();
        }

        return null;
    }

    private function deleteConfig(int $id): void
    {
        $sql = "DELETE FROM `cmw_mail_config_smtp` WHERE mail_config_id = :id;";

        $db = self::getInstance();
        $res = $db->prepare($sql);

        $res->execute(array("id" => $id));

    }

}