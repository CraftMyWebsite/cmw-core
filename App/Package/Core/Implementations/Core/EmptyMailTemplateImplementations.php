<?php

namespace CMW\Implementation\Core\Core;

use CMW\Interface\Core\IMailTemplate;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;

class EmptyMailTemplateImplementations implements IMailTemplate
{
    public function getName(): string
    {
        return LangManager::translate('core.mail.implementations.emptyMail');
    }

    public function getVarName(): string
    {
        return 'empty';
    }

    public function getCode(): string
    {
        return '[MAIL_CONTENT]';
    }

    public function getPreviewImg(): string
    {
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER').'App/Package/Core/Views/Mail/Resources/Images/empty.png';
    }
}