<?php

use CMW\Controller\Core\CoreController;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Theme\Loader\ThemeLoader;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Manager\Views\View;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\Website;

/* @var \CMW\Controller\Core\CoreController $core */
/* @var string $title */
/* @var string $description */
/* @var array $includes */

$siteName = Website::getWebsiteName();
?>
    <!--
      .oooooo.                       .o88o.     .        ooo        ooooo                  oooooo   oooooo     oooo            .o8                 o8o      .
     d8P'  `Y8b                      888 `"   .o8        `88.       .888'                   `888.    `888.     .8'            "888                 `"'    .o8
    888          oooo d8b  .oooo.   o888oo  .o888oo       888b     d'888  oooo    ooo        `888.   .8888.   .8'    .ooooo.   888oooo.   .oooo.o oooo  .o888oo  .ooooo.
    888          `888""8P `P  )88b   888      888         8 Y88. .P  888   `88.  .8'          `888  .8'`888. .8'    d88' `88b  d88' `88b d88(  "8 `888    888   d88' `88b
    888           888      .oP"888   888      888         8  `888'   888    `88..8'            `888.8'  `888.8'     888ooo888  888   888 `"Y88b.   888    888   888ooo888
    `88b    ooo   888     d8(  888   888      888 .       8    Y     888     `888'              `888'    `888'      888    .o  888   888 o.  )88b  888    888 . 888    .o
     `Y8bood8P'  d888b    `Y888""8o o888o     "888"      o8o        o888o     .8'                `8'      `8'       `Y8bod8P'  `Y8bod8P' 8""888P' o888o   "888" `Y8bod8P'.fr
                                                                          .o..P'
                                                                          `Y8P'
    -->
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <meta property="og:title" content=<?= $siteName ?>>
        <meta property="og:site_name" content="<?= $siteName ?>">
        <meta property="og:description" content="<?= Website::getDescription() ?>">
        <meta property="og:type" content="website"/>
        <meta property="og:url" content="<?= EnvManager::getInstance()->getValue('PATH_URL') ?>">

        <title><?= Website::getTitle() ?></title>
        <meta name="description" content="<?= Website::getDescription() ?>">

        <meta name="author" content="CraftMyWebsite, <?= $siteName ?>">
        <meta name="publisher" content="<?= $siteName ?>">
        <meta name="copyright" content="CraftMyWebsite, <?= $siteName ?>">
        <meta name="robots" content="follow, index, all"/>


        <!-- Theme style -->
        <link rel="stylesheet" type="text/css" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Css/style.css">
        <link rel="stylesheet" href="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Admin/Resources/Vendors/Fontawesome-free/Css/fa-all.min.css">

        <?= ImagesManager::getFaviconInclude() ?>

        <?php
        View::loadInclude($includes, 'styles');
        ?>

        <script src="<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Js/flowbite.js"></script>

    </head>

    <style>
        @font-face {  font-family: angkor;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Angkor-Regular.ttf");  }
        @font-face {  font-family: ibmplexsans;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/IBMPlexSans-Regular.ttf");  }
        @font-face {  font-family: kanit;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Kanit-Regular.ttf");  }
        @font-face {  font-family: lora;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Lora-Regular.ttf");  }
        @font-face {  font-family: madimione;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/MadimiOne-Regular.ttf");  }
        @font-face {  font-family: ojuju;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Ojuju-Regular.ttf");  }
        @font-face {  font-family: opensans;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/OpenSans-Regular.ttf");  }
        @font-face {  font-family: playfairdisplay;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/PlayfairDisplay-Regular.ttf");  }
        @font-face {  font-family: robotocondensed;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/RobotoCondensed-Regular.ttf");  }
        @font-face {  font-family: robotomono;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/RobotoMono-Regular.ttf");  }
        @font-face {  font-family: robotoslab;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/RobotoSlab-Regular.ttf");  }
        @font-face {  font-family: rubik;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Rubik-Regular.ttf");  }
        @font-face {  font-family: ubuntu;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Ubuntu-Regular.ttf");  }
        @font-face {  font-family: roboto;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Roboto-Regular.ttf");  }
        @font-face {  font-family: unbounded;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Unbounded-Regular.ttf");  }
        @font-face {  font-family: montserrat;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Montserrat-Regular.ttf");  }
        @font-face {  font-family: paytone;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/PaytoneOne-Regular.ttf");  }
        @font-face {  font-family: sora;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Sora-Regular.ttf");  }
        @font-face {  font-family: outfit;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Outfit-Regular.ttf");  }
        @font-face {  font-family: alata;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Alata-Regular.ttf");  }
        @font-face {  font-family: titan;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/TitanOne-Regular.ttf");  }
        @font-face {  font-family: pressstart;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/PressStart2P-Regular.ttf");  }
        @font-face {  font-family: abrilfatface;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/AbrilFatface-Regular.ttf");  }
        @font-face {  font-family: afacadflux;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/AfacadFlux-Regular.ttf");  }
        @font-face {  font-family: amaticsc;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/AmaticSC-Regular.ttf");  }
        @font-face {  font-family: archivo;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Archivo-Regular.ttf");  }
        @font-face {  font-family: cabin;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Cabin-Regular.ttf");  }
        @font-face {  font-family: caveat;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Caveat-Regular.ttf");  }
        @font-face {  font-family: concretone;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/ConcertOne-Regular.ttf");  }
        @font-face {  font-family: crimsonpro;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/CrimsonPro-Regular.ttf");  }
        @font-face {  font-family: exo2;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Exo2-Regular.ttf");  }
        @font-face {  font-family: lato;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Lato-Regular.ttf");  }
        @font-face {  font-family: lobster;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Lobster-Regular.ttf");  }
        @font-face {  font-family: marcellus;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Marcellus-Regular.ttf");  }
        @font-face {  font-family: merriweather;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Merriweather-Regular.ttf");  }
        @font-face {  font-family: noto;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/NotoSerif-Regular.ttf");  }
        @font-face {  font-family: oleo;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/OleoScript-Regular.ttf");  }
        @font-face {  font-family: playwriteausa;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/PlaywriteAUSA-Regular.ttf");  }
        @font-face {  font-family: playwrite;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/PlaywriteIEGuides-Regular.ttf");  }
        @font-face {  font-family: pt;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/PTSerif-Regular.ttf");  }
        @font-face {  font-family: quicksand;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Quicksand-Regular.ttf");  }
        @font-face {  font-family: satisfy;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Satisfy-Regular.ttf");  }
        @font-face {  font-family: silkscreen;  src:url("<?= EnvManager::getInstance()->getValue('PATH_SUBFOLDER') ?>Public/Themes/<?= ThemeLoader::getInstance()->getCurrentTheme()->name() ?>/Assets/Webfonts/Silkscreen-Regular.ttf");  }
    </style>

<body data-cmw-class="global:main_font" class="bg-[#1b1f23] text-white flex flex-col min-h-screen">

<?php
View::loadInclude($includes, 'beforeScript');
echo CoreController::getInstance()->cmwWarn();
?>