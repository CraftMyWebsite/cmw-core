<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Interface\Core\ICaptcha;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Security\HealthReport;
use CMW\Manager\Views\View;
use CMW\Model\Core\CoreModel;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

use function array_filter;
use function current;

/**
 * Class: @SecurityController
 * @package Core
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 0.0.1
 */
class SecurityController extends AbstractController
{
    #[Link(path: '/', method: Link::GET, scope: '/cmw-admin')]
    #[Link('/security', Link::GET, [], '/cmw-admin')]
    private function adminSecurity(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.settings.security');

        $availablesCaptcha = Loader::loadImplementations(ICaptcha::class);

        View::createAdminView('Core', 'Security/security')
            ->addScriptAfter('App/Package/Core/Views/Resources/Js/security.js')
            ->addVariableList([
                'currentCaptcha' => self::getCaptchaType(),
                'availablesCaptcha' => $availablesCaptcha,
            ])
            ->view();
    }

    #[NoReturn]
    #[Link('/security/edit/captcha', Link::POST, [], '/cmw-admin')]
    private function adminSecurityEditCaptchaPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.settings.security');

        // Get captcha value
        $captcha = FilterManager::filterInputStringPost('captcha');

        // Update option in DB
        if (!CoreModel::getInstance()->updateOption('captcha', $captcha)) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'),
            );
            Redirect::redirectPreviousRoute();
        }

        // If we don't want captcha, we don't need to do anything else
        if ($captcha === 'none') {
            Flash::send(
                Alert::SUCCESS,
                LangManager::translate('core.toaster.success'),
                LangManager::translate('core.toaster.config.success'),
            );
            Redirect::redirectPreviousRoute();
        }

        // Get captcha implementation
        $captchaImplementation = current(array_filter(
            Loader::loadImplementations(ICaptcha::class),
            static fn($implementation) => $implementation->getCode() === $captcha
        ));

        if (!$captchaImplementation instanceof ICaptcha) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('core.toaster.internalError'),
            );
            Redirect::redirectPreviousRoute();
        }

        // Process captcha implementation
        $captchaImplementation->adminFormPost();

        Flash::send(
            Alert::SUCCESS,
            LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.config.success'),
        );

        Redirect::redirectPreviousRoute();
    }

    #[NoReturn]
    #[Link('/security/generate/report/health', Link::GET, [], '/cmw-admin')]
    private function adminGenerateReportHealth(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.settings.security.healthReport');

        $healthReport = new HealthReport();

        $reportName = $healthReport->generateReport();

        $report = file_get_contents(EnvManager::getInstance()->getValue('DIR') . "App/Storage/Reports/$reportName");

        View::createAdminView('Core', 'Security/displayHealthReport')
            ->addVariableList(['report' => $report, 'reportName' => $reportName])
            ->addStyle('Admin/Resources/Vendors/Izitoast/iziToast.min.css')
            ->addScriptAfter('Admin/Resources/Vendors/Izitoast/iziToast.min.js')
            ->view();
    }

    #[NoReturn]
    #[Link('/security/delete/report/health', Link::GET, [], '/cmw-admin')]
    private function adminDeleteReportHealth(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.settings.security.healthReport');

        $healthReport = new HealthReport();

        $healthReport->deleteHealthReports();

        Flash::send(Alert::SUCCESS, LangManager::translate('core.toaster.success'),
            LangManager::translate('core.toaster.security.healthReport.delete'));

        Redirect::redirect('cmw-admin/security');
    }

    /**
     * @return string
     * @Desc Get captcha name (none / hcaptcha / recaptcha)
     */
    public static function getCaptchaType(): string
    {
        return CoreModel::getOptionValue('captcha');
    }

    /**
     * @return \CMW\Interface\Core\ICaptcha|false
     * @desc Return @false if no captcha is set, else return the captcha implementation
     */
    public static function getCaptchaImplementation(): ICaptcha|false
    {
        $captcha = self::getCaptchaType();

        if ($captcha === 'none') {
            return false;
        }

        return current(array_filter(
            Loader::loadImplementations(ICaptcha::class),
            static fn($implementation) => $implementation->getCode() === $captcha
        ));
    }

    /**
     * @return void
     * @Desc Get the captcha config value. Theme Side.
     */
    public static function getPublicData(): void
    {
        $captchaImplementation = self::getCaptchaImplementation();

        if (!$captchaImplementation) {
            return;
        }

        $captchaImplementation->show();
    }

    /**
     * @return bool
     * @desc Check if captcha is valid
     */
    public static function checkCaptcha(): bool
    {
        $captchaImplementation = self::getCaptchaImplementation();

        if (!$captchaImplementation) {
            return true;
        }

        return $captchaImplementation->validate();
    }
}
