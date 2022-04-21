<?php
/* Installation cleaner */
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }

    }

    return rmdir($dir);
}

/**
 * @throws JsonException
 */
function cmwPackageInfo(string $package): array
{
    $jsonFile = file_get_contents("app/package/$package/infos.json");
    return json_decode($jsonFile, true, 512, JSON_THROW_ON_ERROR);
}


/*
 * Error management
 */
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

    foreach ($dirFoldersNames as $folder):
        $jsonFile = file_get_contents("app/package/$folder/infos.json");
        $obj = json_decode($jsonFile,true, 512, JSON_THROW_ON_ERROR);
        $res[] = $obj['name'];
    endforeach;

    return $res;
}