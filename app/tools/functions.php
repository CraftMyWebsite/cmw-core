<?php
/* Installation cleaner */

/*
 * Error management
 */

//TODO A refaire...
function bigToaster(): string
{
    $toasters = "";
    if (isset($_SESSION['toaster'])) {
        foreach ($_SESSION['toaster'] as $toaster) {
            $toasterTitle = $toaster['title'];
            $toasterBody = $toaster['body'];
            $toasterClass = $toaster['type'];
            $toasters .= '<script>$(document).Toasts("create", {title: "' . $toasterTitle . '",body: "' . $toasterBody . '",class: "' . $toasterClass . '"});</script>';
        }
        unset($_SESSION['toaster']);
    }
    return $toasters;
}

/**
 * @throws JsonException
 * @desc Get all packages installed
 */
function getAllPackagesInstalled(): array
{
    $dir = "app/package";
    $dirFoldersNames = array_slice(scandir($dir), 2); //For remove '.' '..'
    $res = [];

    foreach ($dirFoldersNames as $folder) {

        $jsonFile = file_get_contents("app/package/$folder/infos.json");
        $obj = json_decode($jsonFile, true, 512, JSON_THROW_ON_ERROR);
        $res[] = $obj['name'];
    }

    return $res;
}

/***
 * @param $arr
 * @desc Return a pretty array
 */
function debugR($arr): void
{
    echo "<pre>";
    echo print_r($arr);
    echo "</pre>";
}

function getClientIp(): string
{
    return getenv('HTTP_CLIENT_IP') ?? getenv('HTTP_X_FORWARDED_FOR')
        ?? getenv('HTTP_X_FORWARDED') ?? getenv('HTTP_FORWARDED_FOR')
        ?? getenv('REMOTE_ADDR') ?? "UNKNOWN";
}