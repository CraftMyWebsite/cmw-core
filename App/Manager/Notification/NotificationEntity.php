<?php

namespace CMW\Manager\Notification;


use CMW\Controller\Core\CoreController;

class NotificationEntity
{
    private int $id;
    private string $package;
    private string $title;
    private string $message;
    private ?string $slug;
    private int $readed;
    private int $readedSilence;
    private string $createdAt;
    private string $UpdatedAt;

    /**
     * @param int $id
     * @param string $package
     * @param string $title
     * @param string $message
     * @param string|null $slug
     * @param int $readed
     * @param string $createdAt
     * @param string $UpdatedAt
     */
    public function __construct(int $id, string $package, string $title, string $message, ?string $slug, int $readed, int $readedSilence, string $createdAt, string $UpdatedAt)
    {
        $this->id = $id;
        $this->package = $package;
        $this->title = $title;
        $this->message = $message;
        $this->slug = $slug;
        $this->readed = $readed;
        $this->readedSilence = $readedSilence;
        $this->createdAt = $createdAt;
        $this->UpdatedAt = $UpdatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPackage(): string
    {
        return $this->package;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function isRead(): bool
    {
        return $this->readed;
    }

    public function isReadSilence(): bool
    {
        return $this->readedSilence;
    }

    public function getCreatedAt(): string
    {
        return CoreController::formatDate($this->createdAt);
    }

    public function getUpdatedAt(): string
    {
        return CoreController::formatDate($this->UpdatedAt);
    }


}