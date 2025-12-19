<?php

namespace CMW\Controller\Users;

use CMW\Entity\Users\UserSettingsEntity;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\TermsModel;
use CMW\Model\Users\UsersModel;
use CMW\Utils\Redirect;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @UsersTermsController
 * @package Users
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/controllers
 */
class UsersTermsController extends AbstractController
{
    /**
     * @throws \DateMalformedStringException
     */
    #[Link('/terms/accept', Link::GET)]
    private function frontTermsAccept(): void
    {
        if (!UserSettingsEntity::getInstance()->getNeedTerms()) { Redirect::redirectToHome();}
        if (!isset($_SESSION['cmw_temp_user_id'])) { Redirect::redirect('login'); }

        $user = UsersModel::getInstance()->getUserById($_SESSION['cmw_temp_user_id']);
        if (!$user) { Redirect::redirect('login');}

        $model = TermsModel::getInstance();
        $activeTypes = $model->getActiveTypes();

        $toReview = [];
        if (!$user->getTermsAcceptedAtUnformatted()) {
            $toReview = $activeTypes;
        } else {
            $toReview = array_values(array_filter(
                $model->getOutdatedTypesSince(new \DateTimeImmutable($user->getTermsAcceptedAtUnformatted())),
                static fn($t) => in_array($t, $activeTypes, true)
            ));
        }

        $terms = [];
        foreach ($toReview as $t) {
            $e = $model->getLatestByType($t);
            if ($e) {
                $terms[$t->value] = $e;
            }
        }

        View::createPublicView('Users', 'terms_accept')
            ->addVariableList(['types' => $toReview, 'terms' => $terms])
            ->view();
    }


    #[NoReturn]
    #[Link('/terms/accept', Link::POST)]
    private function frontTermsAcceptPost(): void
    {
        if (!isset($_SESSION['cmw_temp_user_id'])) { Redirect::redirect('login'); }

        [$accept] = Utils::filterInput('accept_all');
        if ($accept !== '1') {
            Flash::send(Alert::WARNING, LangManager::translate('users.terms.toaster.title'), LangManager::translate('users.terms.toaster.accept'));
            Redirect::redirectPreviousRoute();
        }

        $user = UsersModel::getInstance()->getUserById($_SESSION['cmw_temp_user_id']);
        if (!$user) { Redirect::redirect('login'); }

        // Marquer l’acceptation
        UsersModel::getInstance()->markTermsAccepted($user->getId());


        $useCookies = $_SESSION['cmw_temp_use_cookies'] ?? 0;
        UsersLoginController::getInstance()->loginUser($user, (bool)$useCookies);

        // Clean temp
        unset($_SESSION['cmw_temp_user_id'], $_SESSION['cmw_temp_use_cookies']);

        $returnTo = $_SESSION['return_to'] ?? null;
        header('Location: ' . ($returnTo ? EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . $returnTo : EnvManager::getInstance()->getValue('PATH_SUBFOLDER') . 'profile'));
        unset($_SESSION['return_to']);
        exit;
    }

}
