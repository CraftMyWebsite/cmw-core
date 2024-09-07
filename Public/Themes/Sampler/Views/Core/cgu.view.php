<?php

use CMW\Utils\Website;

/* @var CMW\Entity\Core\ConditionEntity $cgu */

Website::setTitle('CGU');
Website::setDescription(Website::getWebsiteName() . ' CGU');
?>

<section class="page-section">
    <div class="container">
        <?= $cgu->getContent() ?><br>
        <?= $cgu->getLastEditor()->getPseudo() ?>
        <?= $cgu->getUpdate() ?>
    </div>
</section>
