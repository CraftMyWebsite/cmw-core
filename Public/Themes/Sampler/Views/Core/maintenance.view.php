<?php

use CMW\Utils\Website;

/* @var \CMW\Entity\Core\MaintenanceEntity $maintenance */

Website::setTitle('Maintenance');
Website::setDescription('Maintenance en cours sur le site');
?>

<section class="page-section">
    <div class="container">
        <?php if ($maintenance->isEnable()): ?>
            <div>
                <h1><?= $maintenance->getTitle() ?></h1>
            </div>

            <div>
                <p><?= $maintenance->getDescription() ?></p>
            </div>

            <hr>

            <div>
                <h3>Fin de la maintenance: <?= $maintenance->getTargetDateFormatted() ?></h3>
            </div>
        <?php endif; ?>

    </div>
</section>
