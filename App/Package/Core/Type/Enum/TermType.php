<?php
namespace CMW\Type\Core\Enum;

use CMW\Manager\Lang\LangManager;

enum TermType: string
{
    case TERMS_OF_SERVICE = 'terms_of_service';
    case TERMS_OF_SALE    = 'terms_of_sale';
    case PRIVACY_POLICY   = 'privacy_policy';
    case LEGAL_NOTICE     = 'legal_notice';
    case COOKIE_POLICY    = 'cookie_policy';
    case ACCEPTABLE_USE   = 'acceptable_use';
    case LICENSE          = 'license';
    case REFUND_POLICY    = 'refund_policy';

    public static function values(): array
    {
        return array_map(static fn(self $c) => $c->value, self::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::TERMS_OF_SERVICE => LangManager::translate('core.terms.type.TERMS_OF_SERVICE'),
            self::TERMS_OF_SALE    => LangManager::translate('core.terms.type.TERMS_OF_SALE'),
            self::PRIVACY_POLICY   => LangManager::translate('core.terms.type.PRIVACY_POLICY'),
            self::LEGAL_NOTICE     => LangManager::translate('core.terms.type.LEGAL_NOTICE'),
            self::COOKIE_POLICY    => LangManager::translate('core.terms.type.COOKIE_POLICY'),
            self::ACCEPTABLE_USE   => LangManager::translate('core.terms.type.ACCEPTABLE_USE'),
            self::LICENSE          => LangManager::translate('core.terms.type.LICENSE'),
            self::REFUND_POLICY    => LangManager::translate('core.terms.type.REFUND_POLICY'),
        };
    }

    public function isMandatory(): bool
    {
        return match ($this) {
            self::TERMS_OF_SERVICE,
            self::PRIVACY_POLICY,
            self::LEGAL_NOTICE,
            self::COOKIE_POLICY => true,
            default => false,
        };
    }
}