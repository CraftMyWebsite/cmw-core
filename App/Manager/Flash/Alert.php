<?php

namespace CMW\Manager\Flash;

use JetBrains\PhpStorm\ExpectedValues;

class Alert
{

    public const SUCCESS = "success";
    public const ERROR = "error";
    public const WARNING = "warning";

    public function __construct(
        #[ExpectedValues(flagsFromClass: Alert::class)]
        private readonly string $alertType,
        private readonly string $alertTitle,
        private readonly string $alertMessage,
        private readonly bool   $isAdmin
    )
    {
        $_SESSION["alerts"] ??= array();
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