<?php

/* @var \CMW\Entity\Core\MaintenanceEntity $maintenance */

use CMW\Utils\Website;

Website::setTitle('Maintenance');
Website::setDescription('Maintenance en cours sur le site');
?>
<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif;?>

<h1><?= $maintenance->getTitle() ?></h1>
<p><?= $maintenance->getDescription() ?></p>
<h3>Fin de la maintenance: <?= $maintenance->getTargetDateFormatted() ?></h3>
