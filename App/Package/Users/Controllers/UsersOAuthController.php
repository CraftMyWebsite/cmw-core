<?php

namespace CMW\Controller\Users;

use CMW\Interface\Users\IUsersOAuth;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Loader\Loader;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

class UsersOAuthController extends AbstractController
{

    #[NoReturn] #[Link('/oAuth/:identifier', Link::GET, ['identifier' => '.*?'], '/register')]
    private function register(string $identifier): void
    {
        $implementation = $this->getImplementation($identifier);


        if ($implementation === null) {
            Redirect::errorPage(404);
        }

        // If we have an error while registering
        if (!$implementation->register()) {
            Flash::send(
                Alert::ERROR,
                LangManager::translate('core.toaster.error'),
                'An error occurred while registering.',
            );
            Redirect::redirectPreviousRoute();
        }


        Redirect::redirect('profile');
    }


    /**
     * @return \CMW\Interface\Users\IUsersOAuth[]
     */
    public function getImplementations(): array
    {
        return Loader::loadImplementations(IUsersOAuth::class);
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