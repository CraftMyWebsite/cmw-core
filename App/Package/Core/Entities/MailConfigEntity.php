<?php

namespace CMW\Entity\Core;

class MailConfigEntity
{
    private ?int $id;
    private ?string $mail;
    private ?string $mailReply;
    private ?string $addressSMTP;
    private ?string $user;
    private ?string $password;
    private ?int $port;
    private ?string $protocol;
    private ?string $footer;
    private ?int $enable;

    /**
     * @param int|null $id
     * @param string|null $mail
     * @param string|null $mailReply
     * @param string|null $addressSMTP
     * @param string|null $user
     * @param string|null $password
     * @param int|null $port
     * @param string|null $protocol
     * @param string|null $footer
     * @param int|null $enable
     */
    public function __construct(?int $id, ?string $mail, ?string $mailReply, ?string $addressSMTP, ?string $user,
        ?string $password, ?int $port, ?string $protocol, ?string $footer, ?int $enable)
    {
        $this->id = $id;
        $this->mail = $mail;
        $this->mailReply = $mailReply;
        $this->addressSMTP = $addressSMTP;
        $this->user = $user;
        $this->password = $password;
        $this->port = $port;
        $this->protocol = $protocol;
        $this->footer = $footer;
        $this->enable = $enable;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getMail(): ?string
    {
        return $this->mail;
    }

    /**
     * @return string|null
     */
    public function getMailReply(): ?string
    {
        return $this->mailReply;
    }

    /**
     * @return string|null
     */
    public function getAddressSMTP(): ?string
    {
        return $this->addressSMTP;
    }

    /**
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @return string|null
     */
    public function getProtocol(): ?string
    {
        return $this->protocol;
    }

    /**
     * @return string|null
     */
    public function getFooter(): ?string
    {
        return $this->footer;
    }

    /**
     * @return int|null
     */
    public function isEnable(): ?int
    {
        return $this->enable;
    }
}
