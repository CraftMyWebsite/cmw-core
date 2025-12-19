<?php

namespace CMW\Controller\Core;

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Manager\Flash\Alert;
use CMW\Manager\Flash\Flash;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Package\AbstractController;
use CMW\Manager\Router\Link;
use CMW\Manager\Views\View;
use CMW\Model\Core\TermsModel;
use CMW\Type\Core\Enum\TermType;
use CMW\Utils\Redirect;
use JetBrains\PhpStorm\NoReturn;

/**
 * Class: @TermsController
 * @package Core
 * @author Zomb
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/controllers
 */
class TermsController extends AbstractController
{
    #[Link('/terms', Link::GET, [], '/cmw-admin')]
    private function termsDashboard(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.settings.conditions');

        $lastestTerms = TermsModel::getInstance()->getLatestPerType();
        $byType = [];
        foreach ($lastestTerms as $term) {
            $byType[$term->getType()->value] = $term;
        }

        View::createAdminView('Core', 'Terms/manage')
            ->addScriptBefore('Admin/Resources/Vendors/Tinymce/tinymce.min.js', 'Admin/Resources/Vendors/Tinymce/Config/full.js')
            ->addVariableList(['types' => TermType::cases(), 'terms'  => $byType])
            ->view();
    }

    #[NoReturn]
    #[Link('/terms', Link::POST, [], '/cmw-admin')]
    private function termsDashboardPost(): void
    {
        UsersController::redirectIfNotHavePermissions('core.dashboard', 'core.settings.conditions');

        $userId = UsersSessionsController::getInstance()->getCurrentUser()?->getId();
        if (is_null($userId)) {
            Flash::send(Alert::ERROR, LangManager::translate('core.toaster.error'), LangManager::translate('core.toaster.internalError'));
            Redirect::redirectPreviousRoute();
        }

        $payload = $_POST['term'] ?? [];
        $model   = new TermsModel();
        $published = 0;

        foreach (TermType::cases() as $type) {
            $key = $type->value;
            if (!isset($payload[$key])) continue;

            $content = trim((string)($payload[$key]['content'] ?? ''));
            $requires = isset($payload[$key]['requires_accept']) && $payload[$key]['requires_accept'] === '1';

            $isActive = isset($payload[$key]['is_active']) && $payload[$key]['is_active'] === '1';
            try {
                $model->setTypeActive($type, $isActive);
            } catch (\RuntimeException $e) {

            }

            // Dernière version pour comparaison
            $latest = $model->getLatestByType($type);
            $prevContent   = $latest?->getContent() ?? '';
            $prevRequires  = $latest?->getRequireAccept() ?? true;

            $changedContent  = hash('sha256', $content) !== hash('sha256', $prevContent);
            $changedRequires = (bool)$requires !== (bool)$prevRequires;

            if ($changedContent || $changedRequires) {
                $id = $model->publish($type, $content, $requires, (int)$userId);
                if ($id) $published++;
            }
        }

        Flash::send(
            Alert::SUCCESS,
            LangManager::translate('core.toaster.success'),
            $published > 0
                ? sprintf(LangManager::translate('core.terms.toaster.savedN'), ['d' => $published])
                : LangManager::translate('core.terms.toaster.noChange')
        );

        Redirect::redirectPreviousRoute();
    }

    /* //////////////////// FRONT PUBLIC //////////////////// */

    #[Link('/all_terms', Link::GET)]
    private function frontAllPublic(): void
    {
        $model = TermsModel::getInstance();

        $latestActive = $model->getLatestPerTypeActive();
        $byType = [];
        foreach ($latestActive as $term) {
            $byType[$term->getType()->value] = $term;
        }

        View::createPublicView('Core', 'Terms/all')
            ->addVariableList([
                'terms' => $byType,
                'types' => $model->getActiveTypes(),
            ])
            ->view();
    }

    private function renderTermPublic(TermType $type, string $template): void
    {
        $model = TermsModel::getInstance();
        if (!$model->isTypeActive($type)) {
            Redirect::errorPage(404);
        }

        $term = $model->getLatestByType($type);
        if (!$term) {
            Redirect::errorPage(404);
        }

        View::createPublicView('Core', $template)
            ->addVariableList(['term' => $term])
            ->view();
    }

    #[Link('/cgu', Link::GET)]
    #[Link('/terms_of_service', Link::GET)]
    private function frontToServicePublic(): void
    {
        $this->renderTermPublic(TermType::TERMS_OF_SERVICE, 'Terms/terms_of_service');
    }

    #[Link('/cgv', Link::GET)]
    #[Link('/terms_of_sale', Link::GET)]
    private function frontToSalesPublic(): void
    {
        $this->renderTermPublic(TermType::TERMS_OF_SALE, 'Terms/terms_of_sale');
    }

    #[Link('/privacy_policy', Link::GET)]
    private function frontPrivacyPolicyPublic(): void
    {
        $this->renderTermPublic(TermType::PRIVACY_POLICY, 'Terms/privacy_policy');
    }

    #[Link('/legal_notice', Link::GET)]
    private function frontLegalNoticePublic(): void
    {
        $this->renderTermPublic(TermType::LEGAL_NOTICE, 'Terms/legal_notice');
    }

    #[Link('/cookie_policy', Link::GET)]
    private function frontCookiePolicyPublic(): void
    {
        $this->renderTermPublic(TermType::COOKIE_POLICY, 'Terms/cookie_policy');
    }

    #[Link('/acceptable_use', Link::GET)]
    private function frontAcceptableUsePublic(): void
    {
        $this->renderTermPublic(TermType::ACCEPTABLE_USE, 'Terms/acceptable_use');
    }

    #[Link('/terms_of_license', Link::GET)]
    private function frontLicensePublic(): void
    {
        $this->renderTermPublic(TermType::LICENSE, 'Terms/license');
    }

    #[Link('/refund_policy', Link::GET)]
    private function frontRefundPolicyPublic(): void
    {
        $this->renderTermPublic(TermType::REFUND_POLICY, 'Terms/refund_policy');
    }
}
