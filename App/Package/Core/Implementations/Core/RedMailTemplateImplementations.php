<?php

namespace CMW\Implementation\Core\Core;

use CMW\Interface\Core\IMailTemplate;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Lang\LangManager;
use CMW\Utils\Website;

class RedMailTemplateImplementations implements IMailTemplate
{
    public function getName(): string
    {
        return LangManager::translate('core.mail.implementations.redMail');
    }

    public function getVarName(): string
    {
        return 'red';
    }

    public function getCode(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #E63A5C;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
        }
        .content {
            padding: 20px;
            color: #333;
            font-size: 16px;
            line-height: 1.6;
        }
        a {
            display: inline-block;
            padding: 12px 20px;
            margin: 20px 0;
            background-color: #E63A5C;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .footer {
            background-color: #f4f4f4;
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #777;
        }
        @media screen and (max-width: 600px) {
            .content {
                padding: 15px;
            }
            .header {
                font-size: 20px;
            }
            a {
                width: 100%;
                text-align: center;
                display: block;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        ' . Website::getWebsiteName() . '
    </div>
    <div class="content">
        [MAIL_CONTENT]
    </div>
    <div class="footer">
    © '. date('Y') .'  '. Website::getWebsiteName() . '
    </div>
</div>
</body>
</html>';
    }

    public function getPreviewImg(): string
    {
        return EnvManager::getInstance()->getValue('PATH_SUBFOLDER').'App/Package/Core/Views/Mail/Resources/Images/red.png';
    }
}