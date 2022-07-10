<?php use CMW\Controller\CoreController;

/* @var \CMW\Controller\CoreController $core */
/* @var string $title */
/* @var string $description */

?>

<!DOCTYPE html>
<html lang="fr-FR">
<head>

    <?= $core->cmwHead($title, $description) ?>

    <!-- Theme style -->
    <link rel="stylesheet" type="text/css"
          href="<?= getenv("PATH_SUBFOLDER") ?>public/themes/Sampler/assets/css/main.css">
    <?php
    if (!empty($includes))
        includeFiles($includes, "scriptsBefore", "styles");
    ?>
</head>
<body>
    <?= $core->cmwWarn() ?>


