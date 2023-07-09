<?php

use CMW\Utils\Utils;
use CMW\Utils\Website;

/* @var CMW\Entity\Core\ConditionEntity $cgv */

/*TITRE ET DESCRIPTION*/
$title = Website::getWebsiteName() . ' - CGV';
$description = "CGV de " . Website::getWebsiteName();
?>

<section class="page-section">
    <div class="container">
        <?= $cgv->getContent() ?><br>
        <?= $cgv->getLastEditor()->getPseudo() ?>
        <?= $cgv->getUpdate() ?>
    </div>
</section>

