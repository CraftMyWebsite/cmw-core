<?php

namespace CMW\Controller\Users;

use CMW\Event\Users\LoginEvent;
use CMW\Event\Users\RegisterEvent;
use CMW\Interface\Users\IUsersOAuth;
use CMW\Manager\Events\Emitter;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersOAuthModel;
use CMW\Type\Users\OAuthLoginStatus;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;
use function is_null;

class UsersOAuthController extends AbstractController
{
    #[NoReturn] #[Link('/oauth', Link::GET, [], '/cmw-admin/users')]
    private function oAuthSettingsAdmin(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.oauth');

        $implementations = $this->getImplementations();

        View::createAdminView('Users', 'oAuth/manage')
            ->addVariableList(['implementations' => $implementations])
            ->view();

    }

    #[NoReturn] #[Link('/oauth', Link::POST, [], '/cmw-admin/users')]
    private function oAuthSettingsAdminPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'users.oauth');

        $implementations = $this->getImplementations();

        //Do the post for each implementation
        foreach ($implementations as $implementation) {
            $implementation->adminFormPost();
        }

        //Clear all enabled OAuth implementations
        if (!UsersOAuthModel::getInstance()->clearOAuthImplementationsEnabled()) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                LangManager::translate('users.oauth.flash.saveSettingFailed'),
            );
            Redirect::redirectPreviousRoute();
        }

        // Enable oAuth methods
        foreach ($_POST['oauth_enabled'] ?? [] as $key => $value) {
            $data = FilterManager::filterData($key);

            if (!$this->getImplementation($data)) {
                Flash::send(
                    Alert::ERROR,
                    LangManager::translate('core.toaster.error'),
                    "The $data oAuth implementation does not exist.",
                );
                Redirect::redirectPreviousRoute();
            }

            if (!UsersOAuthModel::getInstance()->enableOAuthImplementation($data)) {
                Flash::send(
                    Alert::ERROR,
                    LangManager::translate('core.toaster.error'),
                    "An error occurred while saving the $data oAuth implementation.",
                );
                Redirect::redirectPreviousRoute();
            }
        }

        Flash::send(
            Alert::SUCCESS,
            LangManager::translate('core.toaster.success'),
            LangManager::translate('users.oauth.flash.saved'),
        );
        Redirect::redirectPreviousRoute();
    }

    /**
     * @throws \Exception
     */
    #[NoReturn] #[Link('/:implementation/connect', Link::GET, ['implementation' => '.*?'], '/api/oauth')]
    private function oAuthApiConnect(string $implementation): void
    {
        $implementation = FilterManager::filterData($implementation);

        $iImplementation = $this->getImplementation($implementation);

        if (is_null($iImplementation)) {
            Redirect::errorPage(404);
        }

        $status = $iImplementation->register();

        switch ($status) {
            case OAuthLoginStatus::INVALID_CODE || OAuthLoginStatus::INVALID_TOKEN:
                Flash::send(
                    Alert::ERROR,
                    LangManager::translate('core.toaster.error'),
                    LangManager::translate('users.oauth.flash.accessDenied')
                );
                break;
            case OAuthLoginStatus::INVALID_USER_INFO:
                Flash::send(
                    Alert::ERROR,
                    LangManager::translate('core.toaster.error'),
                    LangManager::translate('users.oauth.flash.userInfo')
                );
                break;
            case OAuthLoginStatus::EMAIL_ALREADY_EXIST:
                Flash::send(
                    Alert::WARNING,
                    LangManager::translate('core.toaster.error'),
                    LangManager::translate('users.oauth.flash.emailUsed')
                );
                break;
            case OAuthLoginStatus::UNABLE_TO_CREATE_USER:
                Flash::send(
                    Alert::ERROR,
                    LangManager::translate('core.toaster.error'),
                    LangManager::translate('users.oauth.flash.userCreate')
                );
                break;
            case OAuthLoginStatus::UNABLE_TO_CREATE_OAUTH_USER:
                Flash::send(
                    Alert::ERROR,
                    LangManager::translate('core.toaster.error'),
                    LangManager::translate('users.oauth.flash.userOauthCreate')
                );
                break;
            case OAuthLoginStatus::SUCCESS_REGISTER:
                Emitter::send(RegisterEvent::class, UsersModel::getCurrentUser()?->getId());
                Redirect::redirect('profile');
            case OAuthLoginStatus::SUCCESS_LOGIN:
                Emitter::send(LoginEvent::class, UsersModel::getCurrentUser()?->getId());
                Redirect::redirect('profile');
        }

        Redirect::redirect('login');
    }


    #[NoReturn] #[Link('/:identifier', Link::GET, ['identifier' => '.*?'], '/oauth')]
    private function register(string $identifier): void
    {
        $implementation = $this->getImplementation($identifier);

        if (is_null($implementation)) {
            Redirect::errorPage(404);
        }

        // Redirect to consent page. The callback URI will return to the internal CMW API route to register the user.
        $implementation->redirectToConsent();
    }


    /**
     * @return \CMW\Interface\Users\IUsersOAuth[]
     */
    public function getImplementations(): array
    {
        return Loader::loadImplementations(IUsersOAuth::class);
    }

    /**
     * @return \CMW\Interface\Users\IUsersOAuth[]
     */
    public function getEnabledImplementations(): array
    {
        $implementations = $this->getImplementations();

        $enabledImplementations = UsersOAuthModel::getInstance()->getMethodEnabled();

        $toReturn = [];

        foreach ($implementations as $implementation) {
            if (in_array($implementation->methodIdentifier(), $enabledImplementations, true)) {
                $toReturn[] = $implementation;
            }
        }

        return $toReturn;
    }

    /**
     * @param string $methodeIdentifier
     * @return \CMW\Interface\Users\IUsersOAuth|null
     * @desc Get implementation by methode identifier
     */
    public function getImplementation(string $methodeIdentifier): ?IUsersOAuth
    {
        $implementations = $this->getImplementations();

        foreach ($implementations as $implementation) {
            if ($implementation->methodIdentifier() === $methodeIdentifier) {
                return $implementation;
            }
        }

        return null;
    }
}