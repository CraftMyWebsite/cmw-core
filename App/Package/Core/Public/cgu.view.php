<?php

use CMW\Utils\Website;

/**
 * @var \CMW\Entity\Core\ConditionEntity $cgu
 */

Website::setTitle('CGU');
Website::setDescription("Condition d'utilisation");
?>

<section style="width: 70%;padding-bottom: 6rem;margin: 1rem auto auto;">

<?= $cgu->getContent() ?>

<p>Écrit par <b><?= $cgu->getLastEditor()->getPseudo() ?></b>, mis à jour le <?= $cgu->getUpdate() ?></p>

</section>