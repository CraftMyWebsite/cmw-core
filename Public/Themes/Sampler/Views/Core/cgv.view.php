<?php

use CMW\Utils\Utils;
use CMW\Utils\Website;

/* @var CMW\Entity\Core\ConditionEntity $cgv */

/*TITRE ET DESCRIPTION*/
$title = Website::getName() . ' - CGV';
$description = "CGV de " . Website::getName();
?>

<section class="page-section">
    <div class="container">
        <?= $cgv->getContent() ?><br>
        <?= $cgv->getLastEditor()->getPseudo() ?>
        <?= $cgv->getUpdate() ?>
    </div>
</section>

