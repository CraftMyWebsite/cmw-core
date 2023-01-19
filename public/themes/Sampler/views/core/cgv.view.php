<?php

use CMW\Utils\Utils;

/* @var CMW\Entity\Core\ConditionEntity $cgv */

/*TITRE ET DESCRIPTION*/
$title = Utils::getSiteName() . ' - CGV';
$description = "Description de votre page";
?>

<section class="page-section">
    <div class="container">
        <?= $cgv->getConditionContent() ?><br>
        <?= $cgv->getConditionAuthor()->getUsername() ?>
        <?= $cgv->getConditionUpdate() ?>
    </div>
</section>

