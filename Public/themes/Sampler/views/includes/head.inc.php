<?php

use CMW\Utils\Utils;
use CMW\Manager\Views\View;

/* @var \CMW\Controller\Core\CoreController $core */
/* @var string $title */
/* @var string $description */
/* @var array $includes */

?>
    <!DOCTYPE html>
    <html lang="<?= Utils::getEnv()->getValue('LOCALE') ?>>">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
        <?= $core->cmwHead($title, $description) ?>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon"
              href="<?= getenv("PATH_SUBFOLDER") ?>Public/Themes/Sampler/Assets/favicon.ico"/>
        <!-- Core theme CSS (Includes Bootstrap)-->
        <link href="<?= getenv("PATH_SUBFOLDER") ?>Public/Themes/Sampler/Resources/Assets/Css/main.css"
              rel="stylesheet"/>
        <?php
        View::loadInclude($includes, "beforeScript", "styles");
        ?>
    </head>
    <body id="page-top">
<?= $core->cmwWarn() ?>