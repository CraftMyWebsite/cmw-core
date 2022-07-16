<?php

namespace CMW\Utils;

class ErrorManager
{


    public static function redirectError(int $errorCode): void {

        $prefixLink = isset($_SERVER["HTTPS"]) ? "https" : "http";

        //Here, we get data page we don't want to redirect user, just show him an error.
        //Route /error get error file : $errorCode.view.php, if that file don't exist, we call default.view.php (from errors package)
        $data = file_get_contents("$prefixLink://{$_SERVER["HTTP_HOST"]}/geterror/$errorCode");

        if(!$data) {
            echo "Error $errorCode.";
            return;
        }

        $data = str_replace("{errorCode}", $errorCode, $data);
        echo $data;

    }

}