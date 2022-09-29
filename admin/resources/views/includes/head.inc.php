<?php

use CMW\Utils\Images;

?>
<head>
    <meta charset="utf-8" />
    <title>CraftMyWebsite | <?=/** @var string $title */ $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="robots" content="NOINDEX, NOFOLLOW">
    <meta name="description" content="<?=/** @var string $description */  htmlspecialchars_decode($description, ENT_QUOTES) ?>">
    
    <?= Images::getFaviconInclude() ?>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="<?=getenv("PATH_SUBFOLDER")?>admin/resources/vendors/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=getenv("PATH_SUBFOLDER")?>admin/resources/css/adminlte.min.css">
    <!-- Main css file -->
    <link rel="stylesheet" href="<?=getenv("PATH_SUBFOLDER")?>admin/resources/css/main.css">

    <script src="<?=getenv("PATH_SUBFOLDER")?>admin/resources/vendors/jquery/jquery.min.js"></script>

    <?= (isset($styles) && !empty($styles)) ? $styles : "" ?>
</head>
