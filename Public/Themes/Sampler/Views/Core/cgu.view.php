<?php

use CMW\Utils\Website;

/* @var CMW\Entity\Core\ConditionEntity $cgu */

/*TITRE ET DESCRIPTION*/
$title = Website::getName() . ' - CGU';
$description = "Description de votre page";
?>

<section class="page-section">
    <div class="container">
        <?= $cgu->getContent() ?><br>
        <?= $cgu->getLastEditor()->getPseudo() ?>
        <?= $cgu->getUpdate() ?>
    </div>
</section>
