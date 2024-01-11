<?php

use CMW\Utils\Website;

/* @var \CMW\Entity\Pages\PageEntity $pageContent */


Website::setTitle("Builder test");
Website::setDescription("yo");
?>

<div id="gjs">
    <h1><?= $pageContent ?></h1>
</div>