<?php

namespace CMW\Manager\Download;

use CMW\Exception\Core\Download\DownloadException;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Permission\PermissionManager;
use CMW\Model\Users\PermissionsModel;
use JetBrains\PhpStorm\ExpectedValues;
use ZipArchive;

class DownloadManager
{
    /**
     * @param string $url
     * @param string $type
     * @param string $name
     * @return void
     * @desc Download and install package with api return link, ex: "/Public/market/Resources/forum.zip"
     * @throws DownloadException
     */
    public static function installPackageWithLink(string $url, #[ExpectedValues(['package', 'Theme'])] string $type, string $name): void
    {
        if (!in_array($type, ['package', 'Theme'], true)) {
            throw new DownloadException('Type invalide');
        }

        $baseDir = EnvManager::getInstance()->getValue('DIR');
        $zipPath = $baseDir . 'Public/resource.zip';

        $stream = @fopen($url, 'rb');
        if ($stream === false) {
            throw new DownloadException('Impossible de télécharger le fichier distant (API)');
        }

        $bytes = @file_put_contents($zipPath, $stream);
        if ($bytes === false) {
            throw new DownloadException('Impossible d\'écrire le fichier ZIP sur le disque');
        }

        $archiveUpdate = new ZipArchive();
        $openResult = $archiveUpdate->open($zipPath);

        if ($openResult !== true) {
            throw new DownloadException('Impossible d\'ouvrir l\'archive ZIP (code: ' . $openResult . ')');
        }

        $targetDir = $type === 'package' ? $baseDir . 'App/Package' : $baseDir . 'Public/Themes';

        if (!$archiveUpdate->extractTo($targetDir)) {
            $archiveUpdate->close();
            throw new DownloadException('Erreur lors de l\'extraction de l\'archive ZIP (Permissions ou Espace disque)');
        }

        $archiveUpdate->close();
        @unlink($zipPath);

        if ($type === 'package') {
            self::initPackages($name);
        }
    }

    public static function initPackages(string ...$packages): void
    {
        foreach ($packages as $package):
            $initFolder = EnvManager::getInstance()->getValue('dir') . "App/Package/$package/Init";

            if (!is_dir($initFolder)) {
                continue;
            }

            $initFiles = array_diff(scandir($initFolder), ['..', '.']);

            if (empty($initFiles)) {
                continue;
            }

            // Load permissions files
            $packagePermissions = PermissionManager::getPackagePermissions($package);

            if (!is_null($packagePermissions)) {
                foreach ($packagePermissions->permissions() as $permission) {
                    PermissionsModel::getInstance()->addFullCodePermission($permission);
                }
            }

            // Load sql file
            $sqlFile = "$initFolder/init.sql";
            if (file_exists($sqlFile)) {
                $db = DatabaseManager::getLiteInstance();
                $devMode = EnvManager::getInstance()->getValue('DEVMODE');

                $querySqlFile = file_get_contents($sqlFile);

                if (!empty($querySqlFile)) {
                    $req = $db->query($querySqlFile);
                    $req->closeCursor();
                }

                if ($devMode === '0') {
                    unlink($sqlFile);
                }
            }
        endforeach;
    }
}
