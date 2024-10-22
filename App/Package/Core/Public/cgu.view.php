<?php

use CMW\Utils\Website;

/**
 * @var \CMW\Entity\Core\ConditionEntity $cgu
 */

Website::setTitle('CGU');
Website::setDescription("Condition d'utilisation");
?>

<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif;?>

<?= $cgu->getContent() ?>

<p>Écrit par <b><?= $cgu->getLastEditor()->getPseudo() ?></b>, mis à jour le <?= $cgu->getUpdate() ?></p>
