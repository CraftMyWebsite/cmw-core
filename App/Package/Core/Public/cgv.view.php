<?php

use CMW\Utils\Website;

/**
 * @var \CMW\Entity\Core\ConditionEntity $cgv
 */

Website::setTitle('CGV');
Website::setDescription('Condition de vente');
?>

<?php if (\CMW\Controller\Users\UsersController::isAdminLogged()): ?>
    <div style="background-color: orange; padding: 6px; margin-bottom: 10px">
        <span>Votre thème ne gère pas cette page !</span>
        <br>
        <small>Seuls les administrateurs voient ce message !</small>
    </div>
<?php endif;?>

<?= $cgv->getContent() ?>

<p>Écrit par <b><?= $cgv->getLastEditor()->getPseudo() ?></b>, mis à jour le <?= $cgv->getUpdate() ?></p>
