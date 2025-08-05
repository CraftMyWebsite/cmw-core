<?php

namespace CMW\Manager\Security;

use CMW\Controller\Core\PackageController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Theme\Loader\ThemeLoader;
use CMW\Manager\Theme\ThemeManager;
use CMW\Manager\Updater\UpdatesManager;
use CMW\Model\Core\CoreModel;
use DateTime;

class HealthReport
{
    /**
     * @return string
     * @desc <p> Generate CMW Health Report for help our support team.</p>
     *       <b>Please don't share this report to everyone!</b>
     *       <br>
     *       <b> Please delete all reports after sending this to CMW support team.</b>
     */
    public function generateReport(): string
    {
        $generationDate = date(CoreModel::getInstance()->fetchOption('dateFormat'));
        $generatorName = UsersSessionsController::getInstance()->getCurrentUser()?->getPseudo();

        // PHP
        $phpVersion = $this->getPhpVersion();
        $phpExtensions = '';

        foreach ($this->getPhpExtensions() as $name => $version) {
            $phpExtensions .= "     - $name @$version" . "\n";
        }

        $phpSessionDuration = $this->getPhpSessionDuration();
        $phpPostMaxUpload = $this->getPhpPostMaxUpload();

        // Server
        $serverOs = $this->getServerOs();
        $serverTimeZone = EnvManager::getInstance()->getValue('TIMEZONE');
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'];

        // Website
        $websitePathSubFolder = EnvManager::getInstance()->getValue('PATH_SUBFOLDER');
        $websiteDir = EnvManager::getInstance()->getValue('DIR');
        $websiteUrl = EnvManager::getInstance()->getValue('PATH_URL');
        $websiteDevMode = EnvManager::getInstance()->getValue('DEVMODE');
        $websiteUpdateChecker = EnvManager::getInstance()->getValue('UPDATE_CHECKER');

        // SQL
        $sqlVersion = $this->getMySqlVersion();
        $sqlIsMariaDB = $this->isMariaDB() ? 'True' : 'False';

        // Packages
        $packages = '';
        foreach (PackageController::getInstalledPackages() as $package) {
            $packages .= '      - ' . $package->name() . ' @' . $package->version() . " \n ";
        }

        // Themes
        $themes = '';
        foreach (ThemeLoader::getInstance()->getInstalledThemes() as $theme) {
            $isActiveTheme = ThemeLoader::getInstance()->getCurrentTheme()->name() === $theme->name();

            $themes .= '        - ' . $theme->name() . ' @' . $theme->version();

            $themes .= $isActiveTheme ? ' [ACTIVE]' : '';

            $themes .= " \n ";
        }

        // CMS
        $cmsVersion = UpdatesManager::getVersion();

        $data =
            <<<EOL
            ################### CraftMyWebsite - Health Reporting ################### 
             
             Generation date: $generationDate
             Generator name: $generatorName
             
             
             ----- PHP -----
                => Version: $phpVersion
                => Extensions:
             $phpExtensions
                => Session duration: $phpSessionDuration
                => Post max upload: $phpPostMaxUpload
             
             ----- SERVER -----
                => OS: $serverOs
                => TimeZone: $serverTimeZone
                => ServerSoftware: $serverSoftware
             
             ----- WEBSITE -----
                => SUBFOLDER => $websitePathSubFolder
                => DIR => $websiteDir
                => URL => $websiteUrl
                => DevMode => $websiteDevMode
                => Update checker => $websiteUpdateChecker
             
             ----- SQL -----
                => Version: $sqlVersion
                => MariaDB: $sqlIsMariaDB
             
             ----- PACKAGES -----
                => Packages:
             $packages
                 
             ----- THEMES -----
                => Themes:
             $themes
                 
             ----- CMS -----
                 => Version: $cmsVersion
                
            EOL;

        $fileName = 'report_' . (new DateTime())->format('Y-m-d_H\hi\ms\s') . '.txt';

        file_put_contents(EnvManager::getInstance()->getValue('DIR') . 'App/Storage/Reports/' . $fileName, $data);

        return $fileName;
    }

    /**
     * @return void
     * @desc Delete all stored health reports.
     */
    public function deleteHealthReports(): void
    {
        $files = glob(EnvManager::getInstance()->getValue('DIR') . 'App/Storage/Reports/*');

        foreach ($files as $file) {
            if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
                unlink($file);
            }
        }
    }

    private function getPhpVersion(): string
    {
        return PHP_VERSION;
    }

    private function getPhpExtensions(): array
    {
        $extensions = get_loaded_extensions();

        $toReturn = [];

        foreach ($extensions as $extension) {
            $toReturn[$extension] = phpversion($extension);
        }

        return $toReturn;
    }

    private function getPhpSessionDuration(): int
    {
        return ini_get('session.gc_maxlifetime');
    }

    private function getPhpPostMaxUpload(): string
    {
        return ini_get('upload_max_filesize');
    }

    private function getServerOs(): string
    {
        return PHP_OS;
    }

    private function getMySqlVersion(): string
    {
        $db = DatabaseManager::getInstance();
        $version = $db->query('select version()')->fetchColumn();

        preg_match('/^[0-9.]+/', $version, $match);

        return $match[0];
    }

    private function isMariaDB(): bool
    {
        return DatabaseManager::isMariadb();
    }
}
