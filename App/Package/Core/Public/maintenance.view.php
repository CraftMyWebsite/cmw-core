<?php

/* @var \CMW\Entity\Core\MaintenanceEntity $maintenance */

use CMW\Utils\Website;

Website::setTitle('Maintenance');
Website::setDescription('Maintenance en cours sur le site');
?>
<section style="width: 70%;padding-bottom: 6rem;margin: 1rem auto auto;">

<h1><?= $maintenance->getTitle() ?></h1>
<p><?= $maintenance->getDescription() ?></p>
<h3>Fin de la maintenance: <?= $maintenance->getTargetDateFormatted() ?></h3>
</section>