<?php use CMW\Controller\Core\CoreController;
use CMW\Model\Core\ThemeModel;
use CMW\Utils\View;

/* @var \CMW\Controller\CoreController $core */
/* @var string $title */
/* @var string $description */
/* @var array $includes */

?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>

    <?= $core->cmwHead($title, $description) ?>

    <!-- Theme style -->
    <link rel="stylesheet" type="text/css"
          href="<?= getenv("PATH_SUBFOLDER") ?>public/themes/Sampler/assets/css/main.css">
    <?php
    View::loadInclude($includes, "beforeScript", "styles");
    ?>
</head>
<style>
    * {
        background-color: <?= ThemeModel::fetchConfigValue('backgroundColor') ?> !important;
    }
</style>
<body>
<?= $core->cmwWarn() ?>


