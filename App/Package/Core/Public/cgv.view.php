<?php

use CMW\Utils\Website;

/**
 * @var \CMW\Entity\Core\ConditionEntity $cgv
 */

Website::setTitle('CGV');
Website::setDescription('Condition de vente');
?>

<section style="width: 70%;padding-bottom: 6rem;margin: 1rem auto auto;">

<?= $cgv->getContent() ?>

<p>Écrit par <b><?= $cgv->getLastEditor()->getPseudo() ?></b>, mis à jour le <?= $cgv->getUpdate() ?></p>

</section>