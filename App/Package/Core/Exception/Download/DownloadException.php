<?php

namespace CMW\Exception\Core\Download;

use Exception;

/**
 * Class: @DownloadException
 * @package Core
 * @author Zomb
 * @version 1.0
 */
class DownloadException extends Exception
{
    /**
     * @param $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [$this->code]: $this->message\n";
    }
}
