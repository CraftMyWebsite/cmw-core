<?php

namespace CMW\Router;

use Exception;

/**
 * Class: @routerException
 * @package Router
 * @author CraftMywebsite <contact@craftmywebsite.fr>
 * @version 1.0
 */
class RouterException extends Exception
{
    public function __construct($message = null, $code = 403)
    {
        $message ??= 'Unknown ' . get_class($this);
        parent::__construct($message, $code);
    }
}