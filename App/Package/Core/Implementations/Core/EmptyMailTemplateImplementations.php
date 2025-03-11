<?php

namespace CMW\Implementation\Core\Core;

use CMW\Interface\Core\IMailTemplate;
use CMW\Manager\Env\EnvManager;

class EmptyMailTemplateImplementations implements IMailTemplate
{
    public function getName(): string
    {
        return 'Sans style';
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