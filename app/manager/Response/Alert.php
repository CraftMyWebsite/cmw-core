<?php

namespace CMW\Manager\Response;

use JetBrains\PhpStorm\ExpectedValues;

class Alert
{

    public function __construct(
        #[ExpectedValues(["success", "error", "warning"])]
        private readonly string $alertType,
        private readonly string $alertTitle,
        private readonly string $alertMessage
    )
    {
    }

    /**
     * @return string
     */
    #[ExpectedValues(["success", "error", "warning"])]
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
    
    

}