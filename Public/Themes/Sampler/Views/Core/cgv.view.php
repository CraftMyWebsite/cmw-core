<?php

use CMW\Utils\Website;

/* @var CMW\Entity\Core\ConditionEntity $cgv */

Website::setTitle('CGV');
Website::setDescription(Website::getWebsiteName() . ' CGV');
?>

<section class="page-section">
    <div class="container">
        <?= $cgv->getContent() ?><br>
        <?= $cgv->getLastEditor()->getPseudo() ?>
        <?= $cgv->getUpdate() ?>
    </div>
</section>

