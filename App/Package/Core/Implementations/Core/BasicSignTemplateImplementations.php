<?php

namespace CMW\Implementation\Core\Core;

use CMW\Interface\Core\IMailTemplate;
use CMW\Manager\Env\EnvManager;
use CMW\Utils\Website;

class BasicSignTemplateImplementations implements IMailTemplate
{
    public function getName(): string
    {
        return 'Sans style - Avec signature';
    }

    public function getVarName(): string
    {
        return 'empty_signed';
    }

    public function getCode(): string
    {
        return '
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}
</style>
<body>
<p>[MAIL_CONTENT]</p>
<p>--</p>
<img src="'. Website::getUrl() .Website::getFavicon() .'">
<p><a href="' . Website::getUrl() . '">' . Website::getWebsiteName() . '</a></p>
</body>';
    }

    public function getPreviewImg(): string
    {
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER').'App/Package/Core/Views/Mail/Resources/Images/signed.png';
    }
}