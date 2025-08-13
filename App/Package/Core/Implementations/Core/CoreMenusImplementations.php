<?php

namespace CMW\Implementation\Core\Core;

use CMW\Interface\Core\IMenus;
use CMW\Manager\Lang\LangManager;
use CMW\Type\Core\Enum\TermType;

class CoreMenusImplementations implements IMenus
{
    public function getRoutes(): array
    {
        return [
            LangManager::translate('core.home') => 'home',
            LangManager::translate('core.nolink') => '',
            TermType::TERMS_OF_SERVICE->label() => 'terms_of_service',
            TermType::TERMS_OF_SALE->label() => 'terms_of_sale',
            TermType::PRIVACY_POLICY->label() => 'privacy_policy',
            TermType::LEGAL_NOTICE->label() => 'legal_notice',
            TermType::COOKIE_POLICY->label() => 'cookie_policy',
            TermType::ACCEPTABLE_USE->label() => 'acceptable_use',
            TermType::LICENSE->label() => 'terms_of_license',
            TermType::REFUND_POLICY->label() => 'refund_policy',
        ];
    }

    public function getPackageName(): string
    {
        return 'Core';
    }
}
