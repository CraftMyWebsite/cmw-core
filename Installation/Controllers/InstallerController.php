<?php

namespace CMW\Controller\Installer;

use CMW\Exception\Core\Download\DownloadException;
use CMW\Manager\Api\PublicAPI;
use CMW\Manager\Download\DownloadManager;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Error\ErrorManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Router\LinkStorage;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Theme\File\ThemeFileManager;
use CMW\Manager\Theme\Loader\ThemeLoader;
use CMW\Manager\Theme\ThemeManager;
use CMW\Manager\Views\View;
use CMW\Manager\Xml\SitemapManager;
use CMW\Model\Core\CoreModel;
use CMW\Model\Installer\InstallerModel;
use CMW\Utils\Directory;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use CMW\Utils\Website;
use JetBrains\PhpStorm\NoReturn;
use function array_key_exists;
use function base64_decode;
use function date_default_timezone_get;
use function explode;
use function extension_loaded;
use function filter_input;
use function filter_var;
use function header;
use function in_array;
use function is_array;
use function is_file;
use function json_encode;
use function mb_strtolower;
use function ob_start;
use function password_hash;
use const FILTER_VALIDATE_EMAIL;
use const FILTER_VALIDATE_IP;
use const INPUT_POST;
use const JSON_THROW_ON_ERROR;
use const PASSWORD_BCRYPT;
use const PHP_VERSION_ID;

/**
 * Class: @installerController
 * @package installer
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class InstallerController extends AbstractController
{
    static public float $minPhpVersion = 8.3;
    static public int $minPhpVersionId = 80300;
    static public array $requiredSettings = ['php', 'xml', 'mysql', 'gd', 'curl', 'pdo', 'mbstring', 'zip'];

    static public array $installSteps = [0 => 'welcome', 1 => 'config', 2 => 'details', 3 => 'bundle', 4 => 'admin', 5 => 'finish'];

    /**
     * @return bool
     * @desc Check if the website has all the required configurations to start the Installation
     */
    public static function checkAllRequired(): bool
    {
        foreach (self::$requiredSettings as $required) {
            if (!self::hasRequired($required)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $value
     * @return bool
     * @desc Return true if the website has the specified required
     */
    public static function hasRequired(string $value): bool
    {
        return match ($value) {
            'php' => PHP_VERSION_ID >= self::$minPhpVersionId,
            'https' => Website::getProtocol() === 'https',
            'xml' => extension_loaded('xml'),
            'mysql' => extension_loaded('mysql'),
            'gd' => extension_loaded('gd'),
            'curl' => extension_loaded('curl'),
            'pdo' => extension_loaded('pdo'),
            'mbstring' => extension_loaded('mbstring'),
            'zip' => extension_loaded('zip'),
        };
    }

    /**
     * @param string $value
     * @return string
     * @desc Return formatted style
     */
    public static function hasRequiredFormatted(string $value): string
    {
        return self::hasRequired($value)
            ? "<i class='text-green-500 fa-solid fa-check fa-lg'></i>"
            : "<i class='text-red-500 fa-solid fa-xmark fa-xl fa-beat'></i>";
    }

    public static function loadLang(): ?array
    {
        $lang = EnvManager::getInstance()->getValue('locale') ?? 'en';
        $fileName = EnvManager::getInstance()->getValue('dir') . "/Installation/Lang/$lang.php";

        $fileExist = is_file($fileName);

        if (!$fileExist) {
            return null;
        }

        $fileContent = include $fileName;

        if (!is_array($fileContent)) {
            return null;
        }

        return $fileContent;
    }

    #[NoReturn]
    public static function goToInstall(): void
    {
        $path = $_SERVER['REQUEST_URI'];
        $path = explode('/', $path);

        if (in_array('installer', $path)) {
            return;
        }

        ob_start();
        header('Location: installer/');
        die();
    }

    #[Link(path: '/lang/:code', method: Link::GET, variables: ['code' => '.*?'], scope: '/installer')]
    private function changeLang(string $code): void
    {
        EnvManager::getInstance()->setOrEditValue('LOCALE', $code);
        header('location: ../../installer');
    }

    #[Link(path: '/', method: Link::GET, scope: '/installer')]
    private function getInstallPage(): void
    {
        if (self::getInstallationStep() === -1) {
            Redirect::redirectToHome();
        }

        $value = match (self::getInstallationStep()) {
            1 => '1_Database',
            2 => '2_Website',
            3 => '3_Bundle',
            4 => '4_AdminAccount',
            5 => '5_Finish',
            default => '0_Welcome'
        };

        $this->loadView($value);
    }

    public static function getInstallationStep(): int
    {
        return EnvManager::getInstance()->getValue('INSTALLSTEP');
    }

    private function loadView(string $filename): void
    {
        $lang = EnvManager::getInstance()->getValue('locale') ?? 'fr';

        $view = new View('Installation', '');
        $view
            ->setCustomPath(EnvManager::getInstance()->getValue('DIR') . "Installation/Views/$filename.view.php")
            ->setCustomTemplate(EnvManager::getInstance()->getValue('DIR') . 'Installation/Views/template.php')
            ->addStyle('Admin/Resources/Vendors/Izitoast/iziToast.min.css')
            ->addScriptAfter('Admin/Resources/Vendors/Izitoast/iziToast.min.js',
                'Installation/Views/Assets/Js/changeLang.js')
            ->addVariableList(['lang' => $lang]);

        $view->view();
    }

    #[NoReturn]
    #[Link(path: '/submit', method: Link::POST, scope: '/installer', secure: false)]
    private function postInstallPage(): void
    {
        $value = match (self::getInstallationStep()) {
            1 => 'dataBaseInstallPost',
            2 => 'webSiteInstallPost',
            3 => 'bundleInstallPost',
            4 => 'adminAccountInstallPost',
            default => 'welcomePost'
        };

        $this->$value();

        Redirect::redirectPreviousRoute();
    }

    private function welcomePost(): void
    {
        if (!isset($_POST['cgu'])) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('Installation.welcome.error.cgu'));
            Website::refresh();
            return;
        }

        $remoteAddress = $_SERVER['REMOTE_ADDR'];

        if (!filter_var($remoteAddress, FILTER_VALIDATE_IP)) {
            $remoteAddress = '0.0.0.0';
        }

        $data = [
            'domain' => $_SERVER['HTTP_HOST'],
            'cmw_version' => EnvManager::getInstance()->getValue('VERSION'),
            'remote_address' => $remoteAddress,
        ];

        $apiReturn = PublicAPI::postData('websites/register', $data);

        if (array_key_exists('uuid', $apiReturn)) {
            EnvManager::getInstance()->setOrEditValue('CMW_KEY', $apiReturn['uuid']);
        } else {
            EnvManager::getInstance()->setOrEditValue('CMW_KEY', 'ERROR');
            Flash::send(Alert::WARNING, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError') . ' (CMW_KEY)');
            Website::refresh();
        }

        EnvManager::getInstance()->editValue('INSTALLSTEP', 1);
    }

    /**
     * @throws \JsonException
     */
    #[Link(path: '/test/db', method: Link::POST, scope: '/installer', secure: false)]
    private function testDbConnection(): void
    {
        [$host, $username, $passwordEncoded, $port] = Utils::filterInput('bdd_address', 'bdd_login', 'bdd_pass', 'bdd_port');

        $password = base64_decode($passwordEncoded);

        $db = isset($_POST['bdd_name']) ? filter_input(INPUT_POST, 'bdd_name') : 'cmw';

        if (!InstallerModel::tryDatabaseConnection($host, $username, $password, $port)) {
            print (json_encode(['status' => 0,
                'content' => LangManager::translate('core.toaster.db.config.error')],
                JSON_THROW_ON_ERROR));
        } else if (InstallerModel::checkIfDatabaseAlreadyInstalled($host, $username, $password, $db, $port)) {
            print (json_encode(['status' => 0, 'content' =>
                LangManager::translate('core.toaster.db.config.alreadyInstalled')],
                JSON_THROW_ON_ERROR));
        } else {
            print (json_encode(['status' => 1, 'content' =>
                LangManager::translate('core.toaster.db.config.success')],
                JSON_THROW_ON_ERROR));
        }
    }

    private function dataBaseInstallPost(): void
    {
        if (Utils::isValuesEmpty($_POST, 'bdd_name', 'bdd_login', 'bdd_address', 'bdd_port', 'install_folder')) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.db.missing_inputs'));
            return;
        }

        [$host, $username, $password, $port] = Utils::filterInput('bdd_address', 'bdd_login', 'bdd_pass', 'bdd_port');

        $db = filter_input(INPUT_POST, 'bdd_name') ?? 'cmw';

        $subFolder = filter_input(INPUT_POST, 'install_folder');
        $devMode = isset($_POST['dev_mode']) ? 1 : 0;
        $timezone = date_default_timezone_get();  // TODO GET BROWSER TIMEZONE

        if (!InstallerModel::tryDatabaseConnection($host, $username, $password, $port)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.db.config.error'));
            return;
        }

        if (InstallerModel::checkIfDatabaseAlreadyInstalled($host, $username, $password, $db, $port)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.db.config.alreadyInstalled'));
            return;
        }

        $this->firstInstallSetDatabase($host, $db, $username, $password, $port);
        $this->firstInstallSetInfos($subFolder, $timezone, $devMode);

        EnvManager::getInstance()->setOrEditValue('PATH_SUBFOLDER', $subFolder);
        EnvManager::getInstance()->setOrEditValue('PATH_URL', Website::getUrl());
        EnvManager::getInstance()->setOrEditValue('PATH_ADMIN_VIEW', 'Admin/Resources/Views/');
        EnvManager::getInstance()->setOrEditValue('TIMEZONE', $timezone);
        EnvManager::getInstance()->setOrEditValue('DEVMODE', $devMode);
        EnvManager::getInstance()->setOrEditValue('UPDATE_CHECKER', '1');

        // Todo Throw error
        InstallerModel::initDatabase($host, $db, $username, $password, $port);

        // Install the Default Theme settings
        ThemeFileManager::getInstance()->installThemeSettings(ThemeLoader::getInstance()->getCurrentTheme()->name());
        // Init Default routes
        (new LinkStorage())->storeDefaultRoutes();

        EnvManager::getInstance()->editValue('INSTALLSTEP', 2);
    }

    private function firstInstallSetDatabase(string $host, string $db, string $username, string $password, int $port): void
    {
        EnvManager::getInstance()->setOrEditValue('DB_HOST', $host);
        EnvManager::getInstance()->setOrEditValue('DB_NAME', $db);
        EnvManager::getInstance()->setOrEditValue('DB_USERNAME', $username);
        EnvManager::getInstance()->setOrEditValue('DB_PASSWORD', $password);
        EnvManager::getInstance()->setOrEditValue('DB_PORT', $port);
    }

    private function firstInstallSetInfos(string $subFolder, string $timezone, bool $devMode): void
    {
        EnvManager::getInstance()->setOrEditValue('PATH_SUBFOLDER', $subFolder);
        EnvManager::getInstance()->setOrEditValue('TIMEZONE', $timezone);
        EnvManager::getInstance()->setOrEditValue('DEVMODE', $devMode);
    }

    private function webSiteInstallPost(): void
    {
        if (Utils::isValuesEmpty($_POST, 'config_name', 'config_description')) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.db.missing_inputs'));
            Redirect::redirectPreviousRoute();
        }

        $name = filter_input(INPUT_POST, 'config_name');
        $description = filter_input(INPUT_POST, 'config_description');

        InstallerModel::initConfig($name, $description);

        EnvManager::getInstance()->editValue('INSTALLSTEP', 3);
    }

    private function bundleInstallPost(): void
    {
        $isCustom = false;

        if (!isset($_POST['bundleId'])) {
            $isCustom = true;
        }

        // If custom bundle is select, we skip this step
        if ($isCustom) {
            EnvManager::getInstance()->editValue('INSTALLSTEP', 4);
            return;
        }

        $bundleId = $_POST['bundleId'];

        $resources = PublicAPI::putData("market/resources/bundle/install/$bundleId");

        foreach ($resources as $resource) {
            $type = $resource['type'] === 1 ? 'package' : 'Theme';

            try {
                DownloadManager::installPackageWithLink($resource['file'], $type, $resource['name']);
            } catch (DownloadException $e) {
                Flash::send(
                    Alert::WARNING,
                    LangManager::translate('core.toaster.error'),
                    LangManager::translate('core.toaster.theme.unableUpdate') . $e->getMessage(),
                );
                continue;
            }

            if ($type === 'Theme') {
                ThemeFileManager::getInstance()->installThemeSettings($resource['name']);
                CoreModel::getInstance()->updateOption('theme', $resource['name']);
            }
        }

        EnvManager::getInstance()->editValue('INSTALLSTEP', 4);
    }

    private function adminAccountInstallPost(): void
    {
        if (Utils::isValuesEmpty($_POST, 'email', 'pseudo', 'password') || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.db.missing_inputs'));
            return;
        }

        $email = FilterManager::filterInputStringPost('email');
        $pseudo = FilterManager::filterInputStringPost('pseudo');
        $password = password_hash(FilterManager::filterInputStringPost('password'), PASSWORD_BCRYPT);

        $encryptedMail = EncryptManager::encrypt(mb_strtolower($email));

        InstallerModel::initAdmin($encryptedMail, $pseudo, $password);

        EnvManager::getInstance()->editValue('INSTALLSTEP', 5);
    }

    #[NoReturn]
    #[Link(path: '/finish', method: Link::GET, scope: '/installer')]
    private function endInstallation(): void
    {
        // Reset to Default settings (with dev mode or not)
        ErrorManager::enableErrorDisplays();
        EnvManager::getInstance()->editValue('INSTALLSTEP', -1);

        if (EnvManager::getInstance()->getValue('DEVMODE') === '0') {
            if (!Directory::delete(EnvManager::getInstance()->getValue('DIR') . 'Installation')) {
                Flash::send(Alert::ERROR, 'Installation', 'La suppression du dossier d\'installation à échoué !');
            }
        }

        // Init sitemap
        SitemapManager::getInstance()->init();

        // Init robots.txt data
        SitemapManager::getInstance()->initRobotsFile();

        Redirect::redirectToHome();
    }
}
