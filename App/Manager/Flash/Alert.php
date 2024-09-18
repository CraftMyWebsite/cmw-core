<?php

namespace CMW\Manager\Flash;

use JetBrains\PhpStorm\ExpectedValues;

class Alert
{
    public const string SUCCESS = 'success';
    public const string ERROR = 'error';
    public const string WARNING = 'warning';
    public const string INFO = 'warning';

    public function __construct(
        #[ExpectedValues(flagsFromClass: Alert::class)]
        private readonly string $alertType,

        private readonly string $alertTitle,
        private readonly string $alertMessage,
        private readonly bool   $isAdmin,
    )
    {
        $_SESSION['alerts'] ??= [];
    }

    /**
     * @return string
     */
    #[ExpectedValues(flagsFromClass: Alert::class)]
    public function getType(): string
    {
        return $this->alertType;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->alertTitle;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->alertMessage;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }
}
